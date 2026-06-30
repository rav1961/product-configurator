<?php

declare(strict_types=1);

namespace Modules\Catalog\Domain\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Catalog\Domain\Models\Product;

interface ProductRepositoryInterface
{
    /**
     * @return LengthAwarePaginator<int, Product>
     */
    public function paginateActive(?string $categoryPublicId = null, int $perPage = 15): LengthAwarePaginator;

    public function findActiveByPublicId(string $publicId): Product;
}
