<?php

declare(strict_types=1);

namespace App\Enums\Catalog;

enum ProductStatus: string
{
    case DRAFT = 'draft';
    case ACTIVE = 'active';
    case ARCHIVED = 'archived';
}
