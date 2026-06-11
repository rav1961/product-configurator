<?php

declare(strict_types=1);

namespace App\Actions\Catalog;

use App\Enums\Catalog\ProductStatus;
use App\Models\Catalog\Product;
use Illuminate\Pagination\LengthAwarePaginator;

final class ListActiveProductsAction
{
    public const DEFAULT_PER_PAGE = 24;

    public const MAX_PER_PAGE = 100;

    public function execute(
        int $perPage = self::DEFAULT_PER_PAGE,
    ): LengthAwarePaginator {
        return Product::query()
            ->with('category')
            ->where('status', ProductStatus::ACTIVE->value)
            ->whereHas('category', static function ($query): void {
                $query->where('is_active', true);
            })
            ->orderBy('category_id')
            ->orderBy('position')
            ->orderBy('name')
            ->paginate($perPage);
    }
}
