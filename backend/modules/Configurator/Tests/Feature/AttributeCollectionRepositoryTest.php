<?php

declare(strict_types=1);

namespace Modules\Configurator\Tests\Feature;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Catalog\Domain\Models\Product;
use Modules\Configurator\Domain\Contracts\AttributeCollectionRepositoryInterface;
use Modules\Configurator\Domain\Models\AttributeCollection;
use Tests\TestCase;

final class AttributeCollectionRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_ordered_for_product(): void
    {
        $product = Product::factory()->create();

        AttributeCollection::factory()->for($product)->create(['name' => 'Beta', 'position' => 2]);
        AttributeCollection::factory()->for($product)->create(['name' => 'Alpha', 'position' => 1]);
        AttributeCollection::factory()->create();

        $result = app(AttributeCollectionRepositoryInterface::class)->listOrderedForProduct($product->id);

        $this->assertCount(2, $result);
        $this->assertSame(['Alpha', 'Beta'], $result->pluck('name')->all());
    }

    public function test_find_by_public_id(): void
    {
        $collection = AttributeCollection::factory()->create();

        $found = app(AttributeCollectionRepositoryInterface::class)->findByPublicId($collection->public_id);

        $this->assertTrue($found->is($collection));
    }

    public function test_find_by_public_id_throws_when_unknown(): void
    {
        $this->expectException(ModelNotFoundException::class);

        app(AttributeCollectionRepositoryInterface::class)->findByPublicId((string) Str::ulid());
    }
}
