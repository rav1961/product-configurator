<?php

declare(strict_types=1);

namespace Modules\Configurator\Infrastructure\Persistence\Repositories;

use Illuminate\Support\Collection;
use Modules\Configurator\Domain\Contracts\AttributeRepositoryInterface;
use Modules\Configurator\Domain\Models\Attribute;

final class EloquentAttributeRepository implements AttributeRepositoryInterface
{
    /**
     * @return Collection<int, Attribute>
     */
    public function listOrderedForStep(int $stepId): Collection
    {
        return Attribute::query()
            ->where('step_id', $stepId)
            ->ordered()
            ->get();
    }

    public function findByPublicId(string $publicId): Attribute
    {
        return Attribute::query()
            ->where('public_id', $publicId)
            ->firstOrFail();
    }
}
