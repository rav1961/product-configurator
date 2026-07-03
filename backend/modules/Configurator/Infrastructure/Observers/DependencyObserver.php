<?php

declare(strict_types=1);

namespace Modules\Configurator\Infrastructure\Observers;

use Modules\Configurator\Domain\Models\Dependency;
use Modules\Configurator\Domain\Validation\DependencyValidator;

final readonly class DependencyObserver
{
    public function __construct(
        private DependencyValidator $validator,
    ) {}

    public function saving(Dependency $dependency): void
    {
        $this->validator->validate($dependency);
    }
}
