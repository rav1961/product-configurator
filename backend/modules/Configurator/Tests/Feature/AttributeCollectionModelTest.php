<?php

declare(strict_types=1);

namespace Modules\Configurator\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Catalog\Domain\Models\Product;
use Modules\Configurator\Domain\Models\AttributeCollection;
use Tests\TestCase;

final class AttributeCollectionModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_deleting_product_cascades_collections(): void
    {
        $product = Product::factory()->create();

        AttributeCollection::factory()->for($product)->count(2)->create();

        $product->delete();

        $this->assertSame(0, AttributeCollection::query()->count());
    }
}
