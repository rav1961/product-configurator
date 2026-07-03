<?php

declare(strict_types=1);

namespace Modules\Configurator\Domain\Contracts;

use Illuminate\Support\Collection;
use Modules\Configurator\Domain\Models\AttributeValue;

interface AttributeValueRepositoryInterface
{
    /**
     * @return Collection<int, AttributeValue>
     */
    public function listOrderedForAttribute(int $attributeId): Collection;

    /**
     * @return Collection<int, AttributeValue>
     */
    public function listOrderedForCollection(int $collectionId): Collection;

    public function findByPublicId(string $publicId): AttributeValue;
}
