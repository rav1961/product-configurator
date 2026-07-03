<?php

declare(strict_types=1);

namespace Modules\Configurator\Infrastructure\Persistence\Repositories;

use Illuminate\Support\Collection;
use Modules\Configurator\Domain\Contracts\StepRepositoryInterface;
use Modules\Configurator\Domain\Models\Step;

final class EloquentStepRepository implements StepRepositoryInterface
{
    /**
     * @return Collection<int, Step>
     */
    public function listOrderedForProduct(int $productId): Collection
    {
        return Step::query()
            ->where('product_id', $productId)
            ->ordered()
            ->get();
    }

    public function findByPublicId(string $publicId): Step
    {
        return Step::query()
            ->where('public_id', $publicId)
            ->firstOrFail();
    }
}
