<?php

declare(strict_types=1);

namespace Modules\Catalog\Application\DTO;

use Modules\Catalog\Domain\Models\Product;
use Modules\Shared\Application\DTO\MediaData;
use Modules\Shared\Domain\Enums\MediaCollection;
use Modules\Shared\Domain\Enums\MediaProfile;
use Spatie\LaravelData\Data;

final class ProductData extends Data
{
    public function __construct(
        public string $id,
        public string $name,
        public string $slug,
        public ?string $sku,
        public ?string $description,
        public string $status,
        public int $position,
        public ?CategoryData $category,
        public ?MediaData $cover,
    ) {}

    public static function fromModel(Product $product): self
    {
        return new self(
            id: $product->public_id,
            name: $product->name,
            slug: $product->slug,
            sku: $product->sku,
            description: $product->description,
            status: $product->status->value,
            position: $product->position,
            category: $product->relationLoaded('category') && $product->category !== null
                ? CategoryData::fromModel($product->category)
                : null,
            cover: ($cover = $product->getFirstMedia(MediaCollection::Cover->value)) !== null
                ? MediaData::fromMedia($cover, MediaProfile::ProductCover)
                : null,
        );
    }
}
