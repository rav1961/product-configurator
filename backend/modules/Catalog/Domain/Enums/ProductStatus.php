<?php

declare(strict_types=1);

namespace Modules\Catalog\Domain\Enums;

enum ProductStatus: string
{
    case Draft = 'draft';
    case Active = 'active';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Draft => __('products.status.draft'),
            self::Active => __('products.status.active'),
            self::Archived => __('products.status.archived'),
        };
    }
}
