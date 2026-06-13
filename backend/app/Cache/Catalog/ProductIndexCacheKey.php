<?php

declare(strict_types=1);

namespace App\Cache\Catalog;

use App\Data\Catalog\ProductIndexFilters;

final class ProductIndexCacheKey
{
    public function make(ProductIndexFilters $filters, int $page): string
    {
        return implode(':', [
            $this->prefix(),
            'page',
            $page,
            'q',
            $this->normalizeNullable($filters->queryText),
        ]);
    }

    private function prefix(): string
    {
        return (string) config(
            'catalog.products.index_cache.key_prefix',
            'catalog:products:index:v1',
        );
    }

    private function normalizeNullable(mixed $value): string
    {
        if ($value === null || trim((string) $value) === '') {
            return 'none';
        }

        return hash('xxh128', mb_strtolower(trim((string) $value)));
    }
}
