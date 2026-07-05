<?php

declare(strict_types=1);

namespace Modules\Configurator\Infrastructure\Persistence\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Modules\Configurator\Domain\Contracts\DependencyRepositoryInterface;
use Modules\Configurator\Domain\Models\Dependency;

final class EloquentDependencyRepository implements DependencyRepositoryInterface
{
    /**
     * @return Collection<int, Dependency>
     */
    public function listOrderedForProduct(int $productId): Collection
    {
        return Dependency::query()
            ->where('product_id', $productId)
            ->ordered()
            ->get();
    }

    public function findByPublicId(string $publicId): Dependency
    {
        return Dependency::query()
            ->where('public_id', $publicId)
            ->firstOrFail();
    }

    /**
     * @return Collection<int, Dependency>
     */
    public function listOrderedForProductPublicId(string $productPublicId): Collection
    {
        return Dependency::query()
            ->whereHas(
                'product',
                fn (Builder $query): Builder => $query->where('public_id', $productPublicId),
            )
            ->with(['sourceAttribute', 'targetAttribute'])
            ->ordered()
            ->get();
    }
}
