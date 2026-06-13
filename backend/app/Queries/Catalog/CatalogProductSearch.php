<?php

declare(strict_types=1);

namespace App\Queries\Catalog;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class CatalogProductSearch
{
    private const SIMILARITY_THRESHOLD = 0.3;

    public function apply(Builder $query, ?string $queryText): void
    {
        if ($queryText === null || trim($queryText) === '') {
            return;
        }

        $normalized = Str::lower(trim($queryText));
        $escapedLike = str_replace(
            ['\\', '%', '_'],
            ['\\\\', '\\%', '\\_'],
            $normalized
        );
        $containsPattern = '%'.$escapedLike.'%';
        $prefixPattern = $escapedLike.'%';

        if (DB::getDriverName() !== 'pgsql') {
            $this->applyFallbackSearch($query, $containsPattern);

            return;
        }

        $this->applyPostgresSearch(
            query: $query,
            queryText: $queryText,
            normalized: $normalized,
            containsPattern: $containsPattern,
            prefixPattern: $prefixPattern,
        );
    }

    private function applyFallbackSearch(
        Builder $query,
        string $containsPattern
    ): void {
        $query
            ->where(static function (Builder $searchQuery) use ($containsPattern): void {
                $searchQuery
                    ->whereRaw('lower(name) LIKE ?', [$containsPattern])
                    ->orWhereRaw('lower(coalesce(sku, \'\')) LIKE ?', [$containsPattern]);
            })
            ->orderBy('category_id')
            ->orderBy('position')
            ->orderBy('name');
    }

    private function applyPostgresSearch(
        Builder $query,
        string $queryText,
        string $normalized,
        string $containsPattern,
        string $prefixPattern,
    ): void {
        $query
            ->where(static function (Builder $searchQuery) use (
                $queryText,
                $containsPattern,
                $normalized
            ): void {
                $searchQuery
                    ->whereRaw(
                        "to_tsvector('simple', coalesce(name, '') || ' ' || coalesce(sku, '')) @@ websearch_to_tsquery('simple', ?)",
                        [$queryText]
                    )
                    ->orWhereRaw('lower(name) LIKE ?', [$containsPattern])
                    ->orWhereRaw('lower(coalesce(sku, \'\')) LIKE ?', [$containsPattern])
                    ->orWhereRaw('similarity(lower(name), ?) >= ?', [
                        $normalized,
                        self::SIMILARITY_THRESHOLD,
                    ])
                    ->orWhereRaw('similarity(lower(coalesce(sku, \'\')), ?) >= ?', [
                        $normalized,
                        self::SIMILARITY_THRESHOLD,
                    ]);
            })
            ->orderByRaw(
                "CASE
                WHEN lower(coalesce(sku, '')) = ? THEN 0
                WHEN lower(coalesce(sku, '')) LIKE ? THEN 1
                ELSE 2
            END",
                [$normalized, $prefixPattern]
            )
            ->orderByRaw(
                "ts_rank_cd(
                to_tsvector('simple', coalesce(name, '') || ' ' || coalesce(sku, '')),
                websearch_to_tsquery('simple', ?)
            ) DESC",
                [$queryText]
            )
            ->orderByRaw(
                "GREATEST(
                similarity(lower(name), ?),
                similarity(lower(coalesce(sku, '')), ?)
            ) DESC",
                [$normalized, $normalized]
            )
            ->orderBy('position')
            ->orderBy('name');
    }
}
