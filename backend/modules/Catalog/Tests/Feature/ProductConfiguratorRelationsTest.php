<?php

declare(strict_types=1);

namespace Modules\Catalog\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Catalog\Domain\Models\Product;
use Modules\Configurator\Domain\Models\AttributeCollection;
use Modules\Configurator\Domain\Models\Dependency;
use Modules\Configurator\Domain\Models\Step;
use Tests\TestCase;

final class ProductConfiguratorRelationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_exposes_configurator_relations(): void
    {
        $product = Product::factory()->create(['is_configurable' => true]);

        $step = Step::factory()->for($product)->create();
        $collection = AttributeCollection::factory()->for($product)->create();
        $dependency = Dependency::factory()->create(['product_id' => $product->id]);

        $product->refresh();

        $this->assertTrue($product->steps->contains($step));
        $this->assertTrue($product->attributeCollections->contains($collection));
        $this->assertTrue($product->dependencies->contains($dependency));
        $this->assertTrue($step->product->is($product));
    }
}
