<?php

declare(strict_types=1);

namespace Modules\Configurator\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Catalog\Domain\Exceptions\ProductNotConfigurableException;
use Modules\Catalog\Domain\Models\Product;
use Modules\Configurator\Application\Actions\GetConfiguratorSchemaAction;
use Modules\Configurator\Application\DTO\ConfiguratorSchemaData;
use Modules\Configurator\Domain\Models\Attribute;
use Modules\Configurator\Domain\Models\AttributeCollection;
use Modules\Configurator\Domain\Models\AttributeValue;
use Modules\Configurator\Domain\Models\Step;
use Tests\TestCase;

final class GetConfiguratorSchemaActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_execute_returns_schema_for_configurable_product(): void
    {
        $product = Product::factory()->active()->configurable()->create([
            'name' => 'Configurable Sofa',
        ]);
        $step = Step::factory()->for($product)->create([
            'name' => 'Body',
            'position' => 0,
        ]);
        $color = Attribute::factory()->for($step)->select()->create([
            'name' => 'Color',
            'key' => 'color',
            'position' => 0,
            'is_required' => true,
        ]);
        Attribute::factory()->for($step)->create([
            'name' => 'Notes',
            'key' => 'notes',
            'position' => 1,
            'is_required' => false,
        ]);
        AttributeValue::factory()->for($color)->create([
            'label' => 'Red',
            'value' => 'red',
            'position' => 0,
            'is_default' => true,
        ]);

        $schema = app(GetConfiguratorSchemaAction::class)->execute($product->public_id);

        $this->assertInstanceOf(ConfiguratorSchemaData::class, $schema);
        $this->assertSame($product->public_id, $schema->productId);
        $this->assertSame('Configurable Sofa', $schema->productName);
        $this->assertCount(1, $schema->steps);
        $this->assertSame('Body', $schema->steps[0]->name);
        $this->assertCount(2, $schema->steps[0]->attributes);

        $colorAttribute = $schema->steps[0]->attributes[0];
        $this->assertSame('color', $colorAttribute->key);
        $this->assertTrue($colorAttribute->isRequired);
        $this->assertCount(1, $colorAttribute->options);
        $this->assertSame('red', $colorAttribute->options[0]->value);
        $this->assertTrue($colorAttribute->options[0]->isDefault);

        $notesAttribute = $schema->steps[0]->attributes[1];
        $this->assertSame('notes', $notesAttribute->key);
        $this->assertSame([], $notesAttribute->options);
    }

    public function test_execute_resolves_collection_options_for_attribute(): void
    {
        $product = Product::factory()->active()->configurable()->create();
        $step = Step::factory()->for($product)->create();
        $collection = AttributeCollection::factory()->for($product)->create();
        $fabric = Attribute::factory()->for($step)->select()->create([
            'name' => 'Fabric',
            'key' => 'fabric',
            'collection_id' => $collection->id,
        ]);
        AttributeValue::factory()->forCollection($collection)->create([
            'label' => 'Cotton',
            'value' => 'cotton',
            'position' => 0,
        ]);

        $schema = app(GetConfiguratorSchemaAction::class)->execute($product->public_id);

        $attribute = $schema->steps[0]->attributes[0];
        $this->assertSame($fabric->public_id, $attribute->id);
        $this->assertCount(1, $attribute->options);
        $this->assertSame('cotton', $attribute->options[0]->value);
    }

    public function test_execute_returns_empty_steps_when_product_has_no_configuration(): void
    {
        $product = Product::factory()->active()->configurable()->create();

        $schema = app(GetConfiguratorSchemaAction::class)->execute($product->public_id);

        $this->assertSame([], $schema->steps);
    }

    public function test_execute_throws_when_product_is_not_configurable(): void
    {
        $product = Product::factory()->active()->create(['is_configurable' => false]);

        $this->expectException(ProductNotConfigurableException::class);

        app(GetConfiguratorSchemaAction::class)->execute($product->public_id);
    }
}
