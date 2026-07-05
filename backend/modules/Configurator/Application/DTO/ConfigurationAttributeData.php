<?php

declare(strict_types=1);

namespace Modules\Configurator\Application\DTO;

use Modules\Configurator\Domain\Models\Attribute;
use Modules\Configurator\Domain\Models\AttributeValue;
use Spatie\LaravelData\Data;

final class ConfigurationAttributeData extends Data
{
    /**
     * @param  list<ConfigurationOptionData>  $options
     */
    public function __construct(
        public string $id,
        public string $key,
        public string $name,
        public string $type,
        public int $position,
        public bool $isRequired,
        public array $options,
    ) {}

    public static function fromModel(Attribute $attribute): self
    {
        $optionValues = $attribute->usesCollection()
            ? ($attribute->collection->values ?? collect())
            : $attribute->values;

        return new self(
            id: $attribute->public_id,
            key: $attribute->key,
            name: $attribute->name,
            type: $attribute->type->value,
            position: $attribute->position,
            isRequired: $attribute->is_required,
            options: $optionValues
                ->map(static fn (AttributeValue $value): ConfigurationOptionData => ConfigurationOptionData::fromModel($value))
                ->values()
                ->all(),
        );
    }
}
