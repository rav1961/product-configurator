<?php

declare(strict_types=1);

namespace Modules\Catalog\Application\Actions;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Catalog\Application\DTO\ProductIndexData;
use Modules\Catalog\Domain\Contracts\ProductRepositoryInterface;
use Modules\Catalog\Domain\Models\Product;

final readonly class ListProductsAction
{
    public function __construct(
        private ProductRepositoryInterface $products,
    ) {}

    /**
     * @return LengthAwarePaginator<int, Product>
     */
    public function execute(ProductIndexData $data): LengthAwarePaginator
    {
        return $this->products->paginateActive(
            categoryPublicId: $data->categoryPublicId,
            configurableOnly: $data->configurableOnly,
            perPage: $data->perPage,
        );
    }
}
