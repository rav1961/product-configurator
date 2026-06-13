<?php

declare(strict_types=1);

namespace App\Queries\Catalog;

use App\Enums\Catalog\ProductStatus;
use Illuminate\Database\Eloquent\Builder;

final class CatalogProductVisibility
{
    public function apply(Builder $query): void
    {
        $query
            ->where('status', ProductStatus::ACTIVE->value)
            ->whereHas('category', static function (Builder $categoryQuery) {
                $categoryQuery->where('is_active', true);
            });
    }
}
