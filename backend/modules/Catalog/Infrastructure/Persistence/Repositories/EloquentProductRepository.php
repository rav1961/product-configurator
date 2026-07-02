<?php

declare(strict_types=1);

namespace Modules\Catalog\Infrastructure\Persistence\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Modules\Catalog\Domain\Contracts\ProductRepositoryInterface;
use Modules\Catalog\Domain\Models\Product;

final class EloquentProductRepository implements ProductRepositoryInterface
{
    /**
     * @return LengthAwarePaginator<int, Product>
     */
    public function paginateActive(?string $categoryPublicId = null, int $perPage = 15): LengthAwarePaginator
    {
        return Product::query()
            ->active()
            ->when(
                $categoryPublicId !== null,
                static function (Builder $query) use ($categoryPublicId): void {
                    $query->whereHas('category', static function (Builder $relation) use ($categoryPublicId): void {
                        $relation->where('public_id', $categoryPublicId)
                            ->where('is_active', true);
                    });
                },
            )
            ->with(['category', 'media'])
            ->orderBy('position')
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function findActiveByPublicId(string $publicId): Product
    {
        return Product::query()
            ->active()
            ->with(['category', 'media'])
            ->where('public_id', $publicId)
            ->firstOrFail();
    }
}
