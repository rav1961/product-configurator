<?php

declare(strict_types=1);

namespace Modules\Configurator\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Catalog\Domain\Models\Product;
use Modules\Configurator\Application\Actions\ValidateConfigurationAction;
use Modules\Configurator\Domain\Enums\AttributeType;
use Modules\Configurator\Domain\Enums\DependencyAction;
use Modules\Configurator\Domain\Enums\DependencyCondition;
use Modules\Configurator\Domain\Models\Attribute;
use Modules\Configurator\Domain\Models\AttributeValue;
use Modules\Configurator\Domain\Models\Dependency;
use Modules\Configurator\Domain\Models\Step;
use Modules\Configurator\Domain\ValueObjects\ConfigurationSelection;
use Tests\TestCase;

final class ValidateConfigurationActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_execute_validates_visible_required_fields_after_dependency_evaluation(): void
    {
        $product = Product::factory()->active()->configurable()->create();
        $step = Step::factory()->for($product)->create();
        $color = Attribute::factory()->for($step)->select()->create([
            'key' => 'color',
            'is_required' => false,
        ]);
        $finish = Attribute::factory()->for($step)->create([
            'key' => 'finish',
            'type' => AttributeType::Text,
            'is_required' => true,
        ]);

        AttributeValue::factory()->for($color)->create([
            'label' => 'Red',
            'value' => 'red',
        ]);
        AttributeValue::factory()->for($color)->create([
            'label' => 'Blue',
            'value' => 'blue',
            'position' => 1,
        ]);
        Dependency::factory()->create([
            'product_id' => $product->id,
            'source_attribute_id' => $color->id,
            'target_attribute_id' => $finish->id,
            'condition' => DependencyCondition::Equals,
            'condition_value' => 'red',
            'action' => DependencyAction::Show,
            'position' => 0,
        ]);

        $action = app(ValidateConfigurationAction::class);
        $hiddenFinishNotRequired = $action->execute(
            $product->public_id,
            ConfigurationSelection::fromArray(['color' => 'blue']),
        );

        $this->assertTrue($hiddenFinishNotRequired->isValid());

        $visibleFinishRequired = $action->execute(
            $product->public_id,
            ConfigurationSelection::fromArray(['color' => 'red']),
        );
        $this->assertFalse($visibleFinishRequired->isValid());
        $this->assertArrayHasKey('finish', $visibleFinishRequired->errors);

        $complete = $action->execute(
            $product->public_id,
            ConfigurationSelection::fromArray([
                'color' => 'red',
                'finish' => 'matte',
            ]),
        );
        $this->assertTrue($complete->isValid());
    }

    public function test_execute_rejects_invalid_select_option(): void
    {
        $product = Product::factory()->active()->configurable()->create();
        $step = Step::factory()->for($product)->create();
        $color = Attribute::factory()->for($step)->select()->create([
            'key' => 'color',
            'is_required' => true,
        ]);
        AttributeValue::factory()->for($color)->create([
            'label' => 'Red',
            'value' => 'red',
        ]);

        $result = app(ValidateConfigurationAction::class)->execute(
            $product->public_id,
            ConfigurationSelection::fromArray(['color' => 'green']),
        );

        $this->assertFalse($result->isValid());
        $this->assertArrayHasKey('color', $result->errors);
    }
}
