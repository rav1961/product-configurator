<?php

declare(strict_types=1);

namespace Modules\Configurator\Application\DTO;

use Spatie\LaravelData\Data;

final class ConfigurationValidationResult extends Data
{
    /**
     * @param  array<string, list<string>>  $errors
     */
    public function __construct(
        public bool $valid,
        public array $errors,
    ) {}

    public function isValid(): bool
    {
        return $this->valid;
    }
}
