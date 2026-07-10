<?php

declare(strict_types=1);

namespace Modules\Configurator\Tests\Concerns;

use Modules\Configurator\Application\DTO\ConfigurationAttributeData;
use Modules\Configurator\Application\DTO\ConfigurationAttributeStateData;
use Modules\Configurator\Application\DTO\ConfigurationEvaluationData;
use Modules\Configurator\Application\DTO\ConfigurationOptionData;
use Modules\Configurator\Application\DTO\ConfigurationStepData;
use Modules\Configurator\Application\DTO\ConfiguratorSchemaData;
use Modules\Configurator\Domain\Enums\DependencyAction;
use Modules\Configurator\Domain\Models\Attribute;
use Modules\Configurator\Domain\Models\Dependency;
use Modules\Shared\Domain\Enums\SelectionCondition;
use Spatie\LaravelData\DataCollection;

trait BuildsConfiguratorSchema
{
    protected function schemaWithAttributes(ConfigurationAttributeData ...$attributes): ConfiguratorSchemaData
    {
        $attributeCollection = ConfigurationAttributeData::collect(
            array_values($attributes),
            DataCollection::class,
        )->withoutWrapping();

        $steps = ConfigurationStepData::collect([
            new ConfigurationStepData(
                id: 'step-public-id',
                name: 'Step',
                position: 0,
                attributes: $attributeCollection,
            ),
        ], DataCollection::class)->withoutWrapping();

        return new ConfiguratorSchemaData(
            productId: 'product-public-id',
            productName: 'Product',
            steps: $steps,
        );
    }

    /**
     * @param  list<ConfigurationOptionData>  $options
     */
    protected function schemaAttribute(
        string $key,
        string $name = 'Attribute',
        bool $isRequired = false,
        string $type = 'text',
        array $options = [],
    ): ConfigurationAttributeData {
        return new ConfigurationAttributeData(
            id: "{$key}-public-id",
            key: $key,
            name: $name,
            type: $type,
            position: 0,
            isRequired: $isRequired,
            options: ConfigurationOptionData::collect($options, DataCollection::class)->withoutWrapping(),
        );
    }

    /**
     * @param  list<string>  $values
     */
    protected function schemaSelectAttribute(string $key, array $values, bool $isRequired = true): ConfigurationAttributeData
    {
        return $this->schemaAttribute(
            key: $key,
            name: ucfirst($key),
            isRequired: $isRequired,
            type: 'select',
            options: array_map(
                static fn (string $value): ConfigurationOptionData => new ConfigurationOptionData(
                    id: "{$key}-{$value}",
                    label: ucfirst($value),
                    value: $value,
                    isDefault: false,
                ),
                $values,
            ),
        );
    }

    protected function schemaAttributeState(
        ConfigurationAttributeData $attribute,
        bool $visible,
        bool $required,
        bool $disabled = false,
    ): ConfigurationAttributeStateData {
        return new ConfigurationAttributeStateData(
            id: $attribute->id,
            key: $attribute->key,
            visible: $visible,
            required: $required,
            disabled: $disabled,
        );
    }

    /**
     * @param  array<string, ConfigurationAttributeStateData>  $attributes
     */
    protected function schemaEvaluation(array $attributes): ConfigurationEvaluationData
    {
        return new ConfigurationEvaluationData(
            productId: 'product-public-id',
            attributes: $attributes,
        );
    }

    protected function schemaDependency(
        ConfigurationAttributeData $source,
        ConfigurationAttributeData $target,
        SelectionCondition $condition,
        ?string $conditionValue,
        DependencyAction $action,
        int $position = 0,
    ): Dependency {
        $dependency = new Dependency([
            'condition' => $condition,
            'condition_value' => $conditionValue,
            'action' => $action,
            'position' => $position,
        ]);

        $dependency->setRelation('sourceAttribute', new Attribute([
            'key' => $source->key,
            'public_id' => $source->id,
        ]));
        $dependency->setRelation('targetAttribute', new Attribute([
            'key' => $target->key,
            'public_id' => $target->id,
        ]));

        return $dependency;
    }
}
