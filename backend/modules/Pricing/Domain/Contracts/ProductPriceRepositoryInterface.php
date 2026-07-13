<?php

declare(strict_types=1);

namespace Modules\Pricing\Domain\Contracts;

use Modules\Pricing\Domain\Models\ProductPrice;

interface ProductPriceRepositoryInterface
{
    public function findByProductPublicId(string $publicId): ?ProductPrice;
}
