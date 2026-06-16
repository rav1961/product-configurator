<?php

declare(strict_types=1);

namespace Modules\Catalog\Application\Actions;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Catalog\Domain\Models\Product;

final class ListProductsAction
{
    /**
     * @return LengthAwarePaginator<int, Product>
     */
    public function execute(
        ?string $categoryPublicId = null,
        ?int $perPage = 15
    ): LengthAwarePaginator {
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
            ->with('category')
            ->orderBy('position')
            ->orderBy('name')
            ->paginate($perPage);
    }
}
