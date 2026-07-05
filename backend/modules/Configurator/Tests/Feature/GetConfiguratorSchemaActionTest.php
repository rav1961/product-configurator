<?php

declare(strict_types=1);

namespace Modules\Configurator\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Catalog\Domain\Exceptions\ProductNotConfigurableException;
use Modules\Catalog\Domain\Models\Product;
use Modules\Configurator\Application\Actions\GetConfiguratorSchemaAction;
use Modules\Configurator\Domain\Models\Attribute;
use Modules\Configurator\Domain\Models\AttributeCollection;
use Modules\Configurator\Domain\Models\AttributeValue;
use Modules\Configurator\Domain\Models\Step;
use Tests\TestCase;

final class GetConfiguratorSchemaActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_execute_builds_schema_with_own_and_collection_options(): void
    {
        $product = Product::factory()->active()->configurable()->create();
        $step = Step::factory()->for($product)->create();
        $color = Attribute::factory()->for($step)->select()->create(['key' => 'color']);
        $collection = AttributeCollection::factory()->for($product)->create();
        Attribute::factory()->for($step)->select()->create([
            'key' => 'fabric',
            'collection_id' => $collection->id,
        ]);

        AttributeValue::factory()->for($color)->create(['label' => 'Red', 'value' => 'red']);
        AttributeValue::factory()->forCollection($collection)->create(['label' => 'Cotton', 'value' => 'cotton']);

        $schema = app(GetConfiguratorSchemaAction::class)->execute($product->public_id);

        $this->assertSame($product->public_id, $schema->productId);
        $attributes = collect($schema->allAttributes())->keyBy('key');
        $this->assertSame('red', $attributes['color']->options->first()->value);
        $this->assertSame('cotton', $attributes['fabric']->options->first()->value);
    }

    public function test_execute_throws_when_product_is_not_configurable(): void
    {
        $product = Product::factory()->active()->create(['is_configurable' => false]);

        $this->expectException(ProductNotConfigurableException::class);

        app(GetConfiguratorSchemaAction::class)->execute($product->public_id);
    }
}
