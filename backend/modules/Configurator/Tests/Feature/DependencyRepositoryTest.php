<?php

declare(strict_types=1);

namespace Modules\Configurator\Tests\Feature;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Catalog\Domain\Models\Product;
use Modules\Configurator\Domain\Contracts\DependencyRepositoryInterface;
use Modules\Configurator\Domain\Models\Dependency;
use Tests\TestCase;

final class DependencyRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_ordered_for_product(): void
    {
        $product = Product::factory()->create();

        Dependency::factory()->create(['product_id' => $product->id, 'position' => 5]);
        Dependency::factory()->create(['product_id' => $product->id, 'position' => 1]);
        Dependency::factory()->create();

        $result = app(DependencyRepositoryInterface::class)->listOrderedForProduct($product->id);

        $this->assertCount(2, $result);
        $this->assertSame([1, 5], $result->pluck('position')->all());
    }

    public function test_find_by_public_id(): void
    {
        $dependency = Dependency::factory()->create();

        $found = app(DependencyRepositoryInterface::class)->findByPublicId($dependency->public_id);

        $this->assertTrue($found->is($dependency));
    }

    public function test_find_by_public_id_throws_when_unknown(): void
    {
        $this->expectException(ModelNotFoundException::class);

        app(DependencyRepositoryInterface::class)->findByPublicId((string) Str::ulid());
    }
}
