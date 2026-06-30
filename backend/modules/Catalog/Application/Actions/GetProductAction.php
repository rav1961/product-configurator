<?php

declare(strict_types=1);

namespace Modules\Catalog\Application\Actions;

use Modules\Catalog\Domain\Contracts\ProductRepositoryInterface;
use Modules\Catalog\Domain\Models\Product;

final readonly class GetProductAction
{
    public function __construct(
        private ProductRepositoryInterface $products,
    ) {}

    public function execute(string $publicId): Product
    {
        return $this->products->findActiveByPublicId($publicId);
    }
}
