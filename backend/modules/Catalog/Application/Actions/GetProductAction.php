<?php

declare(strict_types=1);

namespace Modules\Catalog\Application\Actions;

use Modules\Catalog\Domain\Models\Product;

final class GetProductAction
{
    public function execute(string $publicId): Product
    {
        return Product::query()
            ->active()
            ->with('category')
            ->where('public_id', $publicId)
            ->firstOrFail();
    }
}
