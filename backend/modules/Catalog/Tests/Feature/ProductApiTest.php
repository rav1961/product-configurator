<?php

declare(strict_types=1);

namespace Modules\Catalog\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Catalog\Domain\Models\Category;
use Modules\Catalog\Domain\Models\Product;
use Tests\TestCase;

final class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_only_active_products(): void
    {
        Product::factory()->active()->count(2)->create();
        Product::factory()->create();

        $response = $this->getJson('/api/products');

        $response
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure([
                'data' => [
                    ['id', 'name', 'slug', 'sku', 'description', 'status', 'position', 'category'],
                ],
                'meta',
            ]);
    }

    public function test_index_filters_by_category(): void
    {
        $category = Category::factory()->create();

        Product::factory()->active()->for($category)->create();
        Product::factory()->active()->create();

        $response = $this->getJson('/api/products?category='.$category->public_id);

        $response->assertOk()->assertJsonCount(1, 'data');
    }

    public function test_show_returns_active_product_by_public_id(): void
    {
        $product = Product::factory()->active()->create();

        $this->getJson('/api/products/'.$product->public_id)
            ->assertOk()
            ->assertJsonPath('data.id', $product->public_id)
            ->assertJsonPath('data.status', 'active');
    }

    public function test_show_returns_404_for_inactive_product(): void
    {
        $product = Product::factory()->create();

        $this->getJson('/api/products/'.$product->public_id)
            ->assertNotFound();
    }
}
