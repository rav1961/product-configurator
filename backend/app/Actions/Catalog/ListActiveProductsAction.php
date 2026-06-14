<?php

declare(strict_types=1);

namespace App\Actions\Catalog;

use App\Data\Catalog\ProductIndexFilters;
use App\Models\Catalog\Product;
use App\Queries\Catalog\CatalogProductSearch;
use App\Queries\Catalog\CatalogProductVisibility;
use Illuminate\Pagination\LengthAwarePaginator;

final readonly class ListActiveProductsAction
{
    public function __construct(
        private CatalogProductSearch $search,
        private CatalogProductVisibility $visibility,
    ) {}

    public function execute(
        ProductIndexFilters $filters,
    ): LengthAwarePaginator {
        $query = Product::query()->with('category');

        $this->visibility->apply($query);
        $this->search->apply($query, $filters->queryText);

        if (! $filters->hasSearch()) {
            $query
                ->orderBy('category_id')
                ->orderBy('position')
                ->orderBy('name');
        }

        return $query->paginate(
            perPage: $filters->perPage,
            page: $filters->page
        );
    }
}
