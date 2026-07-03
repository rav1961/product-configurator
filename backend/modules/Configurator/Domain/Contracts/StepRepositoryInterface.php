<?php

declare(strict_types=1);

namespace Modules\Configurator\Domain\Contracts;

use Illuminate\Support\Collection;
use Modules\Configurator\Domain\Models\Step;

interface StepRepositoryInterface
{
    /**
     * @return Collection<int, Step>
     */
    public function listOrderedForProduct(int $productId): Collection;

    public function findByPublicId(string $publicId): Step;
}
