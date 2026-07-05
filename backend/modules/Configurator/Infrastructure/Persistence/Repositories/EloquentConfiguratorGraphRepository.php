<?php

declare(strict_types=1);

namespace Modules\Configurator\Infrastructure\Persistence\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Modules\Configurator\Domain\Contracts\ConfiguratorGraphRepositoryInterface;
use Modules\Configurator\Domain\Models\Step;

final class EloquentConfiguratorGraphRepository implements ConfiguratorGraphRepositoryInterface
{
    /**
     * @return Collection<int, Step>
     */
    public function loadStepsForProduct(string $productPublicId): Collection
    {
        return Step::query()
            ->whereHas(
                'product',
                fn (Builder $query): Builder => $query->where('public_id', $productPublicId),
            )
            ->with([
                'attributes' => fn ($query) => $query->ordered(),
                'attributes.values' => fn ($query) => $query->ordered(),
                'attributes.collection.values' => fn ($query) => $query->ordered(),
            ])
            ->ordered()
            ->get();
    }
}
