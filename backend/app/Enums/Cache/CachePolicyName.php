<?php

declare(strict_types=1);

namespace App\Enums\Cache;

enum CachePolicyName: string
{
    case CatalogProductsIndex = 'catalog.products.index';
}
