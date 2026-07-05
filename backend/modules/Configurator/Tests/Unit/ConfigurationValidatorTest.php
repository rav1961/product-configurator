<?php

declare(strict_types=1);

namespace Modules\Configurator\Tests\Unit;

use Modules\Configurator\Application\DTO\ConfigurationAttributeStateData;
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
        $schema = $this->schemaWithAttributes(
            $this->schemaAttribute('color', name: 'Color', isRequired: true),
            $this->schemaAttribute('finish', name: 'Finish', isRequired: true),
        );

        $visibleRequired = $this->schemaEvaluation([
            'color' => new ConfigurationAttributeStateData('color', true, true, false),
            'finish' => new ConfigurationAttributeStateData('finish', false, true, false),
        ]);

        $missingColor = $this->validator->validate(
            $schema,
            $visibleRequired,
            ConfigurationSelection::fromArray([]),
        );
        $this->assertFalse($missingColor->isValid());
        $this->assertArrayHasKey('color', $missingColor->errors);
        $this->assertArrayNotHasKey('finish', $missingColor->errors);

        $valid = $this->validator->validate(
            $schema,
            $visibleRequired,
            ConfigurationSelection::fromArray(['color' => 'red']),
        );
        $this->assertTrue($valid->isValid());
    }

    public function test_rejects_unknown_keys_and_invalid_options(): void
    {
        $schema = $this->schemaWithAttributes(
            $this->schemaSelectAttribute('color', ['red']),
        );
        $evaluation = $this->schemaEvaluation([
            'color' => new ConfigurationAttributeStateData('color', true, true, false),
        ]);

        $unknown = $this->validator->validate(
            $schema,
            $evaluation,
            ConfigurationSelection::fromArray(['unknown' => 'x']),
        );
        $this->assertArrayHasKey('unknown', $unknown->errors);

        $invalidOption = $this->validator->validate(
            $schema,
            $evaluation,
            ConfigurationSelection::fromArray(['color' => 'green']),
        );
        $this->assertArrayHasKey('color', $invalidOption->errors);
    }
}
