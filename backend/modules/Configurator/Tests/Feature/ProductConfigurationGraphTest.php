<?php

declare(strict_types=1);

namespace Modules\Configurator\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Catalog\Domain\Models\Product;
use Modules\Configurator\Domain\Enums\DependencyAction;
use Modules\Configurator\Domain\Models\Attribute;
use Modules\Configurator\Domain\Models\AttributeCollection;
use Modules\Configurator\Domain\Models\AttributeValue;
use Modules\Configurator\Domain\Models\Dependency;
use Modules\Configurator\Domain\Models\Step;
use Modules\Shared\Domain\Enums\SelectionCondition;
use Tests\TestCase;

final class ProductConfigurationGraphTest extends TestCase
{
    use RefreshDatabase;

    public function test_builds_complete_configuration_graph_for_product(): void
    {
        $product = Product::factory()->active()->configurable()->create();
        $step = Step::factory()->for($product)->create([
            'name' => 'Body',
            'position' => 0,
        ]);
        $color = Attribute::factory()->for($step)->select()->create([
            'name' => 'Color',
            'key' => 'color',
            'position' => 0,
        ]);
        $collection = AttributeCollection::factory()->for($product)->create([
            'name' => 'Fabrics',
            'key' => 'fabrics',
        ]);
        $fabric = Attribute::factory()->for($step)->select()->create([
            'name' => 'Fabric',
            'key' => 'fabric',
            'collection_id' => $collection->id,
            'position' => 1,
        ]);
        $finish = Attribute::factory()->for($step)->create([
            'name' => 'Finish',
            'key' => 'finish',
            'position' => 2,
        ]);

        AttributeValue::factory()->for($color)->create([
            'label' => 'Red',
            'value' => 'red',
            'position' => 0,
        ]);
        AttributeValue::factory()->forCollection($collection)->create([
            'label' => 'Cotton',
            'value' => 'cotton',
            'position' => 0,
        ]);
        Dependency::factory()->create([
            'product_id' => $product->id,
            'source_attribute_id' => $color->id,
            'target_attribute_id' => $finish->id,
            'condition' => SelectionCondition::Equals,
            'condition_value' => 'red',
            'action' => DependencyAction::Show,
            'position' => 0,
        ]);

        $product->refresh();

        $this->assertCount(1, Step::query()->where('product_id', $product->id)->get());
        $this->assertTrue($fabric->usesCollection());
        $this->assertCount(1, $color->values);
        $this->assertCount(1, $collection->values);
        $this->assertCount(1, Dependency::query()->where('product_id', $product->id)->get());
    }

    public function test_deleting_product_cascades_entire_configuration_graph(): void
    {
        $product = Product::factory()->configurable()->create();
        $step = Step::factory()->for($product)->create();
        $attribute = Attribute::factory()->for($step)->select()->create();
        $collection = AttributeCollection::factory()->for($product)->create();

        AttributeValue::factory()->for($attribute)->create();
        AttributeValue::factory()->forCollection($collection)->create();

        Attribute::factory()->for($step)->create(['collection_id' => $collection->id]);

        $target = Attribute::factory()->for($step)->create();

        Dependency::factory()->create([
            'product_id' => $product->id,
            'source_attribute_id' => $attribute->id,
            'target_attribute_id' => $target->id,
        ]);

        $product->delete();

        $this->assertSame(0, Step::query()->count());
        $this->assertSame(0, Attribute::query()->count());
        $this->assertSame(0, AttributeCollection::query()->count());
        $this->assertSame(0, AttributeValue::query()->count());
        $this->assertSame(0, Dependency::query()->count());
    }
}
