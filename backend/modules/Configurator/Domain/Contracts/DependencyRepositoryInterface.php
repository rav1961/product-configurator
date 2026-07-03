<?php

declare(strict_types=1);

namespace Modules\Configurator\Domain\Contracts;

use Illuminate\Support\Collection;
use Modules\Configurator\Domain\Models\Dependency;

interface DependencyRepositoryInterface
{
    /**
     * @return Collection<int, Dependency>
     */
    public function listOrderedForProduct(int $productId): Collection;

    public function findByPublicId(string $publicId): Dependency;
}
