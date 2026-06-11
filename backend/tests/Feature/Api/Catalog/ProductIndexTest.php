<?php

declare(strict_types=1);

namespace Tests\Feature\Api\Catalog;

use App\Models\Catalog\Category;
use App\Models\Catalog\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ProductIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_only_active_products(): void
    {
        $category = Category::factory()->create([
            'name' => 'Doors',
        ]);

        $activeProduct = Product::factory()
            ->for($category)
            ->active()
            ->create([
                'name' => 'Premium Door',
                'slug' => 'premium-door',
                'position' => 1,
            ]);

        Product::factory()
            ->for($category)
            ->archived()
            ->create([
                'name' => 'Archived Door',
                'slug' => 'archived-door',
            ]);

        $response = $this->getJson('/api/catalog/products');

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.publicId', $activeProduct->public_id)
            ->assertJsonPath('data.0.name', 'Premium Door')
            ->assertJsonPath('data.0.slug', 'premium-door')
            ->assertJsonPath('data.0.categoryName', 'Doors')
            ->assertJsonPath('meta.total', 1);
    }

    public function test_it_paginates_products(): void
    {
        Product::factory()
            ->active()
            ->count(3)
            ->create();

        $response = $this->getJson('/api/catalog/products?per_page=2');

        $response
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('meta.per_page', 2)
            ->assertJsonPath('meta.total', 3);
    }

    public function test_it_validates_per_page_minimum_value(): void
    {
        $this->getJson('/api/catalog/products?per_page=-1')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['per_page']);
    }

    public function test_it_valid_per_page_maximum_value(): void
    {
        $this->getJson('/api/catalog/products?per_page=101')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['per_page']);
    }

    public function test_it_excludes_products_from_inactive_categories(): void
    {
        $activeCategory = Category::factory()->create([
            'name' => 'Active Category',
            'is_active' => true,
        ]);

        $inactiveCategory = Category::factory()->create([
            'name' => 'Inactive Category',
            'is_active' => false,
        ]);

        $visibleProduct = Product::factory()
            ->for($activeCategory)
            ->active()
            ->create([
                'name' => 'Visible Product',
                'slug' => 'visible-product',
            ]);

        Product::factory()
            ->for($inactiveCategory)
            ->active()
            ->create([
                'name' => 'Hidden Product',
                'slug' => 'hidden-product',
            ]);

        $response = $this->getJson('/api/catalog/products');

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.publicId', $visibleProduct->public_id)
            ->assertJsonPath('data.0.name', 'Visible Product')
            ->assertJsonPath('meta.total', 1);
    }
}
