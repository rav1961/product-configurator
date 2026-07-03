<?php

declare(strict_types=1);

namespace Modules\Configurator\Infrastructure\Persistence\Repositories;

use Illuminate\Support\Collection;
use Modules\Configurator\Domain\Contracts\AttributeValueRepositoryInterface;
use Modules\Configurator\Domain\Models\AttributeValue;

final class EloquentAttributeValueRepository implements AttributeValueRepositoryInterface
{
    /**
     * @return Collection<int, AttributeValue>
     */
    public function listOrderedForAttribute(int $attributeId): Collection
    {
        return AttributeValue::query()
            ->where('attribute_id', $attributeId)
            ->ordered()
            ->get();
    }

    /**
     * @return Collection<int, AttributeValue>
     */
    public function listOrderedForCollection(int $collectionId): Collection
    {
        return AttributeValue::query()
            ->where('collection_id', $collectionId)
            ->ordered()
            ->get();
    }

    public function findByPublicId(string $publicId): AttributeValue
    {
        return AttributeValue::query()
            ->where('public_id', $publicId)
            ->firstOrFail();
    }
}
