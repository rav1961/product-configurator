<?php

declare(strict_types=1);

namespace Modules\Catalog\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Catalog\Domain\Models\Category;
use Modules\Catalog\Domain\Models\Product;
use Modules\Users\Domain\Models\User;
use Tests\TestCase;

final class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_guest_cannot_list_products(): void
    {
        $this->getJson(route('api.products.list'))->assertUnauthorized();
    }

    public function test_guest_cannot_show_product(): void
    {
        $product = Product::factory()->active()->create();

        $this->getJson(route('api.products.show', [
            'productId' => $product->public_id,
        ]))->assertUnauthorized();
    }

    public function test_index_returns_only_active_products(): void
    {
        Product::factory()->active()->count(2)->create();
        Product::factory()->create();

        $response = $this->actingAs($this->user)
            ->getJson(route('api.products.list'));

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

        $response = $this->actingAs($this->user)
            ->getJson(route('api.products.list', [
                'category' => $category->public_id,
            ]));

        $response->assertOk()->assertJsonCount(1, 'data');
    }

    public function test_index_returns_pagination_meta(): void
    {
        Product::factory()->active()->count(3)->create();

        $response = $this->actingAs($this->user)
            ->getJson(route('api.products.list', [
                'per_page' => 2,
                'page' => 2,
            ]));

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
        $this->actingAs($this->user)
            ->getJson(route('api.products.list', [
                'per_page' => 0,
            ]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['per_page']);

        $this->actingAs($this->user)
            ->getJson(route('api.products.list', [
                'per_page' => 101,
            ]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['per_page']);
    }

    public function test_index_returns_422_for_invalid_page(): void
    {
        $this->actingAs($this->user)
            ->getJson(route('api.products.list', [
                'page' => 0,
            ]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['page']);
    }

    public function test_index_returns_422_for_invalid_category_ulid(): void
    {
        $this->actingAs($this->user)
            ->getJson(route('api.products.list', [
                'category' => 'not-a-ulid',
            ]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['category']);
    }

    public function test_index_returns_422_for_unknown_category(): void
    {
        $this->actingAs($this->user)
            ->getJson(route('api.products.list', [
                'category' => (string) Str::ulid(),
            ]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['category']);
    }

    public function test_index_returns_422_for_inactive_category(): void
    {
        $category = Category::factory()->inactive()->create();

        $this->actingAs($this->user)
            ->getJson(route('api.products.list', [
                'category' => $category->public_id,
            ]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['category'])
            ->assertJsonPath('errors.category.0', 'The selected category is not available.');
    }

    public function test_show_returns_active_product_by_public_id(): void
    {
        $product = Product::factory()->active()->create();

        $this->actingAs($this->user)
            ->getJson(route('api.products.show', [
                'productId' => $product->public_id,
            ]))
            ->assertOk()
            ->assertJsonPath('data.id', $product->public_id)
            ->assertJsonPath('data.status', 'active');
    }

    public function test_show_returns_404_for_inactive_product(): void
    {
        $product = Product::factory()->create();

        $this->actingAs($this->user)
            ->getJson(route('api.products.show', [
                'productId' => $product->public_id,
            ]))
            ->assertNotFound();
    }

    public function test_show_returns_404_for_unknown_public_id(): void
    {
        $this->actingAs($this->user)
            ->getJson(route('api.products.show', [
                'productId' => (string) Str::ulid(),
            ]))
            ->assertNotFound();
    }
}
