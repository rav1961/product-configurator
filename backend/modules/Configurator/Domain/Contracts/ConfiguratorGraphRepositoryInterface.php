<?php

declare(strict_types=1);

namespace Modules\Configurator\Domain\Contracts;

use Illuminate\Support\Collection;
use Modules\Configurator\Domain\Models\Step;

interface ConfiguratorGraphRepositoryInterface
{
    /**
     * @return Collection<int, Step>
     */
    public function loadStepsForProduct(string $productPublicId): Collection;
}
