<?php

declare(strict_types=1);

namespace Modules\Configurator\Application\DTO;

use Spatie\LaravelData\Data;

final class ConfigurationEvaluationData extends Data
{
    /**
     * @param  array<string, ConfigurationAttributeStateData>  $attributes
     */
    public function __construct(
        public string $productId,
        public array $attributes,
    ) {}
}
