<?php

declare(strict_types=1);

namespace Tests\Feature\Catalog;

use App\Enums\Catalog\ProductStatus;
use App\Models\Catalog\Category;
use App\Models\Catalog\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class CatalogModelsTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_can_have_products(): void
    {
        $category = Category::factory()->create();

        Product::factory()
            ->for($category)
            ->active()
            ->create();

        $category->refresh();

        $this->assertCount(1, $category->products);
        $this->assertTrue($category->products->first()->isActive());
    }

    public function test_product_status_is_cast_to_enum(): void
    {
        $product = Product::factory()->create([
            'status' => ProductStatus::ACTIVE,
        ]);

        $this->assertSame(ProductStatus::ACTIVE, $product->status);
    }

    public function test_catalog_models_have_public_identifiers(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create();

        $this->assertNotNull($category->public_id);
        $this->assertNotNull($product->public_id);
        $this->assertNotSame((string) $category->id, $category->public_id);
        $this->assertNotSame((string) $product->id, $product->public_id);
    }
}
