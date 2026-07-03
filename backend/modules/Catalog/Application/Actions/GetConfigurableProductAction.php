<?php

declare(strict_types=1);

namespace Modules\Catalog\Application\Actions;

use Modules\Catalog\Application\DTO\ConfigurableProductData;
use Modules\Catalog\Domain\Contracts\ProductRepositoryInterface;
use Modules\Catalog\Domain\Exceptions\ProductNotConfigurableException;

final readonly class GetConfigurableProductAction
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
    ) {}

    public function execute(string $publicId): ConfigurableProductData
    {
        $product = $this->productRepository->findActiveByPublicId($publicId);

        if (! $product->isConfigurable()) {
            throw ProductNotConfigurableException::forPublicId($publicId);
        }

        return ConfigurableProductData::fromModel($product);
    }
}
