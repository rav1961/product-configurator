<?php

declare(strict_types=1);

namespace App\Data\Catalog;

use App\Models\Catalog\Category;
use App\Models\Catalog\Product;
use Spatie\LaravelData\Data;

final class ProductListItemData extends Data
{
    public function __construct(
        public string $publicId,
        public string $name,
        public string $slug,
        public ?string $sku,
        public ?string $shortDescription,
        public ?string $categoryName,
    ) {}

    public static function fromModel(Product $product): self
    {
        $category = $product->category;

        return new self(
            publicId: $product->public_id,
            name: $product->name,
            slug: $product->slug,
            sku: $product->sku,
            shortDescription: $product->short_description,
            categoryName: $category instanceof Category ? $category->name : null,
        );
    }
}
