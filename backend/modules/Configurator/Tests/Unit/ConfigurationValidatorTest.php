<?php

declare(strict_types=1);

namespace Modules\Configurator\Tests\Unit;

use Modules\Configurator\Application\DTO\ConfigurationAttributeData;
use Modules\Configurator\Application\DTO\ConfigurationAttributeStateData;
use Modules\Configurator\Application\DTO\ConfigurationEvaluationData;
use Modules\Configurator\Application\DTO\ConfigurationOptionData;
use Modules\Configurator\Application\DTO\ConfigurationStepData;
use Modules\Configurator\Application\DTO\ConfiguratorSchemaData;
use Modules\Configurator\Domain\Services\ConfigurationValidator;
use Modules\Configurator\Domain\ValueObjects\ConfigurationSelection;
use Tests\TestCase;

final class ConfigurationValidatorTest extends TestCase
{
    private ConfigurationValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new ConfigurationValidator;
    }

    public function test_validates_required_visible_attribute(): void
    {
        $schema = $this->schemaWithAttributes(
            $this->attribute('color', name: 'Color', isRequired: true),
        );
        $evaluation = $this->evaluation([
            'color' => new ConfigurationAttributeStateData('color', true, true, false),
        ]);

        $invalid = $this->validator->validate(
            $schema,
            $evaluation,
            ConfigurationSelection::fromArray([]),
        );

        $this->assertFalse($invalid->isValid());
        $this->assertArrayHasKey('color', $invalid->errors);

        $valid = $this->validator->validate(
            $schema,
            $evaluation,
            ConfigurationSelection::fromArray(['color' => 'red']),
        );

        $this->assertTrue($valid->isValid());
    }

    public function test_skips_hidden_attributes_even_when_required(): void
    {
        $schema = $this->schemaWithAttributes(
            $this->attribute('finish', name: 'Finish', isRequired: true),
        );
        $evaluation = $this->evaluation([
            'finish' => new ConfigurationAttributeStateData('finish', false, true, false),
        ]);

        $result = $this->validator->validate(
            $schema,
            $evaluation,
            ConfigurationSelection::fromArray([]),
        );

        $this->assertTrue($result->isValid());
    }

    public function test_rejects_unknown_attribute_keys(): void
    {
        $schema = $this->schemaWithAttributes(
            $this->attribute('color', name: 'Color'),
        );
        $evaluation = $this->evaluation([
            'color' => new ConfigurationAttributeStateData('color', true, false, false),
        ]);

        $result = $this->validator->validate(
            $schema,
            $evaluation,
            ConfigurationSelection::fromArray(['unknown' => 'x']),
        );

        $this->assertFalse($result->isValid());
        $this->assertArrayHasKey('unknown', $result->errors);
    }

    public function test_validates_select_and_multiselect_options(): void
    {
        $schema = $this->schemaWithAttributes(
            $this->selectAttribute('color', ['red', 'blue']),
            $this->multiselectAttribute('tags', ['a', 'b']),
        );
        $evaluation = $this->evaluation([
            'color' => new ConfigurationAttributeStateData('color', true, true, false),
            'tags' => new ConfigurationAttributeStateData('tags', true, true, false),
        ]);

        $invalid = $this->validator->validate(
            $schema,
            $evaluation,
            ConfigurationSelection::fromArray([
                'color' => 'green',
                'tags' => ['a', 'x'],
            ]),
        );

        $this->assertFalse($invalid->isValid());
        $this->assertArrayHasKey('color', $invalid->errors);
        $this->assertArrayHasKey('tags', $invalid->errors);
        $valid = $this->validator->validate(
            $schema,
            $evaluation,
            ConfigurationSelection::fromArray([
                'color' => 'red',
                'tags' => ['a', 'b'],
            ]),
        );
        $this->assertTrue($valid->isValid());
    }

    private function schemaWithAttributes(ConfigurationAttributeData ...$attributes): ConfiguratorSchemaData
    {
        return new ConfiguratorSchemaData(
            productId: 'product-public-id',
            productName: 'Product',
            steps: [
                new ConfigurationStepData(
                    id: 'step-public-id',
                    name: 'Step',
                    position: 0,
                    attributes: array_values($attributes),
                ),
            ],
        );
    }

    /**
     * @param  list<ConfigurationOptionData>  $options
     */
    private function attribute(
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
            options: $options,
        );
    }

    /**
     * @param  list<string>  $values
     */
    private function selectAttribute(string $key, array $values): ConfigurationAttributeData
    {
        return $this->attribute(
            key: $key,
            name: ucfirst($key),
            isRequired: true,
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

    /**
     * @param  list<string>  $values
     */
    private function multiselectAttribute(string $key, array $values): ConfigurationAttributeData
    {
        return $this->attribute(
            key: $key,
            name: ucfirst($key),
            isRequired: true,
            type: 'multiselect',
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

    /**
     * @param  array<string, ConfigurationAttributeStateData>  $attributes
     */
    private function evaluation(array $attributes): ConfigurationEvaluationData
    {
        return new ConfigurationEvaluationData(
            productId: 'product-public-id',
            attributes: $attributes,
        );
    }
}
