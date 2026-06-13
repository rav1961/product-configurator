<?php

declare(strict_types=1);

namespace App\Actions\Catalog;

use App\Enums\Catalog\ProductStatus;
use App\Models\Catalog\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class ListActiveProductsAction
{
    public const DEFAULT_PER_PAGE = 24;

    public const MAX_PER_PAGE = 100;

    public function execute(
        int $perPage = self::DEFAULT_PER_PAGE,
        ?string $queryText = null,
    ): LengthAwarePaginator {
        $query = Product::query()
            ->with('category')
            ->where('status', ProductStatus::ACTIVE->value)
            ->whereHas('category', static function ($query): void {
                $query->where('is_active', true);
            });

        $this->applySearch($query, $queryText);

        if ($queryText === null || trim($queryText) === '') {
            $query
                ->orderBy('category_id')
                ->orderBy('position')
                ->orderBy('name');
        }

        return $query->paginate($perPage);
    }

    private function applySearch(Builder $query, ?string $queryText): void
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
            $query
                ->where(static function (Builder $searchQuery) use ($containsPattern): void {
                    $searchQuery
                        ->whereRaw('lower(name) LIKE ?', [$containsPattern])
                        ->orWhereRaw('lower(coalesce(sku, \'\')) LIKE ?', [$containsPattern]);
                })
                ->orderBy('category_id')
                ->orderBy('position')
                ->orderBy('name');

            return;
        }

        $query
            ->where(static function (Builder $searchQuery) use ($queryText, $containsPattern, $normalized): void {
                $searchQuery
                    ->whereRaw(
                        "to_tsvector('simple', coalesce(name, '') || ' ' || coalesce(sku, '')) @@ websearch_to_tsquery('simple', ?)",
                        [$queryText]
                    )
                    ->orWhereRaw('lower(name) LIKE ?', [$containsPattern])
                    ->orWhereRaw('lower(coalesce(sku, \'\')) LIKE ?', [$containsPattern])
                    ->orWhereRaw('similarity(lower(name), ?) >= 0.3', [$normalized])
                    ->orWhereRaw('similarity(lower(coalesce(sku, \'\')), ?) >= 0.3', [$normalized]);
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
