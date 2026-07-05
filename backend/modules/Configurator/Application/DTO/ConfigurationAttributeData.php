<?php

declare(strict_types=1);

namespace Modules\Configurator\Application\DTO;

use Modules\Configurator\Domain\Models\Attribute;
use Modules\Configurator\Domain\Models\AttributeValue;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

final class ConfigurationAttributeData extends Data
{
    /**
     * @param  DataCollection<int, ConfigurationOptionData>  $options
     */
    public function __construct(
        public string $id,
        public string $key,
        public string $name,
        public string $type,
        public int $position,
        public bool $isRequired,
        #[DataCollectionOf(ConfigurationOptionData::class)]
        public DataCollection $options,
    ) {}

    public static function fromModel(Attribute $attribute): self
    {
        $optionValues = $attribute->usesCollection()
            ? ($attribute->collection->values ?? collect())
            : $attribute->values;

        $options = ConfigurationOptionData::collect(
            $optionValues
                ->map(static fn (AttributeValue $value): ConfigurationOptionData => ConfigurationOptionData::fromModel($value))
                ->values()
                ->all(),
            DataCollection::class,
        )->withoutWrapping();

        return new self(
            id: $attribute->public_id,
            key: $attribute->key,
            name: $attribute->name,
            type: $attribute->type->value,
            position: $attribute->position,
            isRequired: $attribute->is_required,
            options: $options,
        );
    }
}
