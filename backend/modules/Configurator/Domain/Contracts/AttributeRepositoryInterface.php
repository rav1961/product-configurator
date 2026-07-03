<?php

declare(strict_types=1);

namespace Modules\Configurator\Domain\Contracts;

use Illuminate\Support\Collection;
use Modules\Configurator\Domain\Models\Attribute;

interface AttributeRepositoryInterface
{
    /**
     * @return Collection<int, Attribute>
     */
    public function listOrderedForStep(int $stepId): Collection;

    public function findByPublicId(string $publicId): Attribute;
}
