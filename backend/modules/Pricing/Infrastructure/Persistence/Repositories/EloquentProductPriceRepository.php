<?php

declare(strict_types=1);

namespace Modules\Pricing\Infrastructure\Persistence\Repositories;

use Modules\Pricing\Domain\Contracts\ProductPriceRepositoryInterface;
use Modules\Pricing\Domain\Models\ProductPrice;

final class EloquentProductPriceRepository implements ProductPriceRepositoryInterface
{
    public function findByProductPublicId(string $publicId): ?ProductPrice
    {
        return ProductPrice::query()
            ->whereHas('product', static function ($query) use ($publicId): void {
                $query->where('public_id', $publicId);
            })
            ->first();
    }
}
