<?php

declare(strict_types=1);

namespace Modules\Configurator\Tests\Unit;

use Modules\Configurator\Domain\Services\ConfigurationValidator;
use Modules\Configurator\Domain\ValueObjects\ConfigurationSelection;
use Modules\Configurator\Tests\Concerns\BuildsConfiguratorSchema;
use Tests\TestCase;

final class ConfigurationValidatorTest extends TestCase
{
    use BuildsConfiguratorSchema;

    private ConfigurationValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->validator = new ConfigurationValidator;
    }

    public function test_validates_required_visible_fields_and_skips_hidden(): void
    {
        $color = $this->schemaAttribute('color', name: 'Color', isRequired: true);
        $finish = $this->schemaAttribute('finish', name: 'Finish', isRequired: true);
        $schema = $this->schemaWithAttributes($color, $finish);

        $visibleRequired = $this->schemaEvaluation([
            $color->id => $this->schemaAttributeState($color, visible: true, required: true),
            $finish->id => $this->schemaAttributeState($finish, visible: false, required: true),
        ]);

        $missingColor = $this->validator->validate(
            $schema,
            $visibleRequired,
            ConfigurationSelection::fromArray([]),
        );
        $this->assertFalse($missingColor->isValid());
        $this->assertArrayHasKey($color->id, $missingColor->errors);
        $this->assertArrayNotHasKey($finish->id, $missingColor->errors);

        $valid = $this->validator->validate(
            $schema,
            $visibleRequired,
            ConfigurationSelection::fromArray([$color->id => 'red']),
        );
        $this->assertTrue($valid->isValid());
    }

    public function test_rejects_unknown_keys_and_invalid_options(): void
    {
        $color = $this->schemaSelectAttribute('color', ['red']);
        $schema = $this->schemaWithAttributes($color);
        $evaluation = $this->schemaEvaluation([
            $color->id => $this->schemaAttributeState($color, visible: true, required: true),
        ]);

        $unknown = $this->validator->validate(
            $schema,
            $evaluation,
            ConfigurationSelection::fromArray(['unknown-public-id' => 'x']),
        );
        $this->assertArrayHasKey('unknown-public-id', $unknown->errors);

        $invalidOption = $this->validator->validate(
            $schema,
            $evaluation,
            ConfigurationSelection::fromArray([$color->id => 'green']),
        );
        $this->assertArrayHasKey($color->id, $invalidOption->errors);
    }

    public function test_accepts_numeric_values_for_number_attributes(): void
    {
        $width = $this->schemaAttribute('width', name: 'Width', isRequired: true, type: 'number');
        $schema = $this->schemaWithAttributes($width);
        $evaluation = $this->schemaEvaluation([
            $width->id => $this->schemaAttributeState($width, visible: true, required: true),
        ]);

        $integer = $this->validator->validate(
            $schema,
            $evaluation,
            ConfigurationSelection::fromArray([$width->id => 120]),
        );
        $this->assertTrue($integer->isValid());

        $decimal = $this->validator->validate(
            $schema,
            $evaluation,
            ConfigurationSelection::fromArray([$width->id => 199.99]),
        );
        $this->assertTrue($decimal->isValid());

        $stringNumeric = $this->validator->validate(
            $schema,
            $evaluation,
            ConfigurationSelection::fromArray([$width->id => '120']),
        );
        $this->assertTrue($stringNumeric->isValid());
    }

    public function test_rejects_non_string_values_for_text_attributes(): void
    {
        $label = $this->schemaAttribute('label', name: 'Label', isRequired: true, type: 'text');
        $schema = $this->schemaWithAttributes($label);
        $evaluation = $this->schemaEvaluation([
            $label->id => $this->schemaAttributeState($label, visible: true, required: true),
        ]);

        $result = $this->validator->validate(
            $schema,
            $evaluation,
            ConfigurationSelection::fromArray([$label->id => 120]),
        );

        $this->assertArrayHasKey($label->id, $result->errors);
    }
}
