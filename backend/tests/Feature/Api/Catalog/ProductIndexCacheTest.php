<?php

declare(strict_types=1);

namespace Tests\Feature\Api\Catalog;

use App\Models\Catalog\Category;
use App\Models\Catalog\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

final class ProductIndexCacheTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('cache-policy.defaults.enabled', true);
        config()->set('cache-policy.defaults.store', 'array');
        config()->set('cache-policy.defaults.ttl_seconds', 600);
        config()->set('cache-policy.defaults.jitter_seconds', 0);
        config()->set('cache-policy.defaults.requires_taggable_store', false);

        Cache::flush();
    }

    public function test_it_serves_product_index_payload_from_cache(): void
    {
        $category = Category::factory()->create();

        Product::factory()
            ->for($category)
            ->active()
            ->create([
                'name' => 'Cached Door',
                'slug' => 'cached-door',
                'position' => 1,
            ]);

        $this->getJson('/api/catalog/products')
            ->assertOk()
            ->assertJsonPath('meta.total', 1);

        Product::factory()
            ->for($category)
            ->active()
            ->create([
                'name' => 'Fresh Door',
                'slug' => 'fresh-door',
                'position' => 2,
            ]);

        $this->getJson('/api/catalog/products')
            ->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.name', 'Cached Door');
    }

    public function test_it_uses_different_cache_entries_for_different_query_parameters(): void
    {
        $category = Category::factory()->create();

        Product::factory()
            ->for($category)
            ->active()
            ->create([
                'name' => 'Oak Door',
                'slug' => 'oak-door',
                'sku' => 'DOOR-OAK',
            ]);

        Product::factory()
            ->for($category)
            ->active()
            ->create([
                'name' => 'Steel Gate',
                'slug' => 'steel-gate',
                'sku' => 'GATE-STEEL',
            ]);

        $this->getJson('/api/catalog/products?q=oak')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Oak Door');

        $this->getJson('/api/catalog/products?q=gate')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Steel Gate');
    }
}
