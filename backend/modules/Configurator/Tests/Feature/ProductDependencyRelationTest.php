<?php

declare(strict_types=1);

namespace Modules\Configurator\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Catalog\Domain\Models\Product;
use Modules\Configurator\Domain\Enums\DependencyAction;
use Modules\Configurator\Domain\Exceptions\InvalidDependencyScopeException;
use Modules\Configurator\Domain\Models\Attribute;
use Modules\Configurator\Domain\Models\Dependency;
use Modules\Configurator\Domain\Models\Step;
use Modules\Shared\Domain\Enums\SelectionCondition;
use Tests\TestCase;

final class ProductDependencyRelationTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_dependency_through_product_relation(): void
    {
        $product = Product::factory()->configurable()->create();
        $step = Step::factory()->for($product)->create();
        $source = Attribute::factory()->for($step)->create(['key' => 'color']);
        $target = Attribute::factory()->for($step)->create(['key' => 'finish']);

        $dependency = $product->dependencies()->create([
            'source_attribute_id' => $source->id,
            'target_attribute_id' => $target->id,
            'condition' => SelectionCondition::Equals,
            'condition_value' => 'red',
            'action' => DependencyAction::Show,
            'position' => 0,
        ]);

        $this->assertInstanceOf(Dependency::class, $dependency);
        $this->assertSame($product->id, $dependency->product_id);
        $this->assertCount(1, $product->fresh()->dependencies);
    }

    public function test_rejects_dependency_with_foreign_attributes(): void
    {
        $product = Product::factory()->configurable()->create();
        $foreignStep = Step::factory()->create();
        $source = Attribute::factory()->for($foreignStep)->create();
        $target = Attribute::factory()->for($foreignStep)->create();

        $this->expectException(InvalidDependencyScopeException::class);

        $product->dependencies()->create([
            'source_attribute_id' => $source->id,
            'target_attribute_id' => $target->id,
            'condition' => SelectionCondition::IsSet,
            'condition_value' => null,
            'action' => DependencyAction::Hide,
            'position' => 0,
        ]);
    }

    public function test_rejects_equals_without_condition_value_on_relation_create(): void
    {
        $product = Product::factory()->configurable()->create();
        $step = Step::factory()->for($product)->create();
        $source = Attribute::factory()->for($step)->create();
        $target = Attribute::factory()->for($step)->create();

        $this->expectException(InvalidDependencyScopeException::class);

        $product->dependencies()->create([
            'source_attribute_id' => $source->id,
            'target_attribute_id' => $target->id,
            'condition' => SelectionCondition::Equals,
            'condition_value' => null,
            'action' => DependencyAction::Require,
            'position' => 0,
        ]);
    }
}
