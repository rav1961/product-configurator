<?php

declare(strict_types=1);

namespace Modules\Configurator\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Catalog\Domain\Models\Product;
use Modules\Configurator\Application\Actions\EvaluateConfigurationAction;
use Modules\Configurator\Domain\Enums\DependencyAction;
use Modules\Configurator\Domain\Enums\DependencyCondition;
use Modules\Configurator\Domain\Models\Attribute;
use Modules\Configurator\Domain\Models\Dependency;
use Modules\Configurator\Domain\Models\Step;
use Modules\Configurator\Domain\ValueObjects\ConfigurationSelection;
use Tests\TestCase;

final class EvaluateConfigurationActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_execute_evaluates_dependencies_for_product_configuration(): void
    {
        $product = Product::factory()->active()->configurable()->create();
        $step = Step::factory()->for($product)->create();
        $color = Attribute::factory()->for($step)->create([
            'key' => 'color',
            'is_required' => false,
        ]);
        $finish = Attribute::factory()->for($step)->create([
            'key' => 'finish',
            'is_required' => false,
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

        $action = app(EvaluateConfigurationAction::class);

        $hidden = $action->execute(
            $product->public_id,
            ConfigurationSelection::fromArray(['color' => 'blue']),
        );
        $this->assertFalse($hidden->attributes['finish']->visible);

        $visible = $action->execute(
            $product->public_id,
            ConfigurationSelection::fromArray(['color' => 'red']),
        );
        $this->assertTrue($visible->attributes['finish']->visible);
        $this->assertSame($product->public_id, $visible->productId);
    }
}
