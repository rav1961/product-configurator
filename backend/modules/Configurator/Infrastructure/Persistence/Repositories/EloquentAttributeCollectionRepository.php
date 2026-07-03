<?php

declare(strict_types=1);

namespace Modules\Configurator\Infrastructure\Persistence\Repositories;

use Illuminate\Support\Collection;
use Modules\Configurator\Domain\Contracts\AttributeCollectionRepositoryInterface;
use Modules\Configurator\Domain\Models\AttributeCollection;

final class EloquentAttributeCollectionRepository implements AttributeCollectionRepositoryInterface
{
    /**
     * @return Collection<int, AttributeCollection>
     */
    public function listOrderedForProduct(int $productId): Collection
    {
        return AttributeCollection::query()
            ->where('product_id', $productId)
            ->ordered()
            ->get();
    }

    public function findByPublicId(string $publicId): AttributeCollection
    {
        return AttributeCollection::query()
            ->where('public_id', $publicId)
            ->firstOrFail();
    }
}
