<?php

declare(strict_types=1);

namespace Modules\Configurator\Application\DTO;

use Spatie\LaravelData\Data;

final class ConfigurationAttributeStateData extends Data
{
    public function __construct(
        public string $id,
        public string $key,
        public bool $visible,
        public bool $required,
        public bool $disabled,
    ) {}
}
