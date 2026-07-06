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
}
