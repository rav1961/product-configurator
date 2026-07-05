<?php

declare(strict_types=1);

namespace Modules\Configurator\Application\DTO;

use Modules\Configurator\Domain\Models\Attribute;
use Modules\Configurator\Domain\Models\Step;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

final class ConfigurationStepData extends Data
{
    /**
     * @param  DataCollection<int, ConfigurationAttributeData>  $attributes
     */
    public function __construct(
        public string $id,
        public string $name,
        public int $position,
        #[DataCollectionOf(ConfigurationAttributeData::class)]
        public DataCollection $attributes,
    ) {}

    public static function fromModel(Step $step): self
    {
        $attributes = ConfigurationAttributeData::collect(
            $step->attributes
                ->map(static fn (Attribute $attribute): ConfigurationAttributeData => ConfigurationAttributeData::fromModel($attribute))
                ->values()
                ->all(),
            DataCollection::class,
        )->withoutWrapping();

        return new self(
            id: $step->public_id,
            name: $step->name,
            position: $step->position,
            attributes: $attributes,
        );
    }
}
