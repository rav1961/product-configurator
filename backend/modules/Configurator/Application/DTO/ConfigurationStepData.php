<?php

declare(strict_types=1);

namespace Modules\Configurator\Application\DTO;

use Modules\Configurator\Domain\Models\Attribute;
use Modules\Configurator\Domain\Models\Step;
use Spatie\LaravelData\Data;

final class ConfigurationStepData extends Data
{
    /**
     * @param  list<ConfigurationAttributeData>  $attributes
     */
    public function __construct(
        public string $id,
        public string $name,
        public int $position,
        public array $attributes,
    ) {}

    public static function fromModel(Step $step): self
    {
        return new self(
            id: $step->public_id,
            name: $step->name,
            position: $step->position,
            attributes: $step->attributes
                ->map(static fn (Attribute $attribute): ConfigurationAttributeData => ConfigurationAttributeData::fromModel($attribute))
                ->values()
                ->all(),
        );
    }
}
