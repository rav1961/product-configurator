<?php

declare(strict_types=1);

namespace Modules\Catalog\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
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

    public function test_index_returns_pagination_meta(): void
    {
        Product::factory()->active()->count(3)->create();

        $response = $this->getJson('/api/products?per_page=2&page=2');

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('meta.per_page', 2)
            ->assertJsonPath('meta.current_page', 2)
            ->assertJsonPath('meta.total', 3)
            ->assertJsonStructure([
                'data',
                'links',
                'meta' => [
                    'current_page',
                    'from',
                    'last_page',
                    'per_page',
                    'to',
                    'total',
                ],
            ]);
    }

    public function test_index_returns_422_for_invalid_per_page(): void
    {
        $this->getJson('/api/products?per_page=0')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['per_page']);

        $this->getJson('/api/products?per_page=101')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['per_page']);
    }

    public function test_index_returns_422_for_invalid_page(): void
    {
        $this->getJson('/api/products?page=0')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['page']);
    }

    public function test_index_returns_422_for_invalid_category_ulid(): void
    {
        $this->getJson('/api/products?category=not-a-ulid')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['category']);
    }

    public function test_index_returns_422_for_unknown_category(): void
    {
        $this->getJson('/api/products?category='.Str::ulid())
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['category']);
    }

    public function test_index_returns_422_for_inactive_category(): void
    {
        $category = Category::factory()->inactive()->create();
        $this->getJson('/api/products?category='.$category->public_id)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['category'])
            ->assertJsonPath('errors.category.0', 'The selected category is not available.');
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

    public function test_show_returns_404_for_unknown_public_id(): void
    {
        $this->getJson('/api/products/'.Str::ulid())
            ->assertNotFound();
    }
}
