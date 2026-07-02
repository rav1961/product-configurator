<?php

declare(strict_types=1);

namespace Modules\Catalog\Application\DTO;

use Modules\Catalog\Domain\Models\Category;
use Modules\Shared\Application\DTO\MediaData;
use Modules\Shared\Domain\Enums\MediaCollection;
use Modules\Shared\Domain\Enums\MediaProfile;
use Spatie\LaravelData\Data;

final class CategoryData extends Data
{
    public function __construct(
        public string $id,
        public string $name,
        public string $slug,
        public ?string $description,
        public int $position,
        public ?MediaData $cover,
    ) {}

    public static function fromModel(Category $category): self
    {
        return new self(
            id: $category->public_id,
            name: $category->name,
            slug: $category->slug,
            description: $category->description,
            position: $category->position,
            cover: ($cover = $category->getFirstMedia(MediaCollection::Cover->value)) !== null
                ? MediaData::fromMedia($cover, MediaProfile::CategoryCover)
                : null,
        );
    }
}
