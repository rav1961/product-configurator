<?php

declare(strict_types=1);

namespace Modules\Catalog\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Catalog\Domain\Enums\ProductStatus;
use Modules\Catalog\Domain\Models\Category;
use Modules\Catalog\Domain\Models\Product;
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
            'status' => ProductStatus::Active,
        ]);

        $this->assertSame(ProductStatus::Active, $product->status);
    }

    public function test_catalog_models_have_public_identifier(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create();

        $this->assertTrue(Str::isUlid($category->public_id));
        $this->assertTrue(Str::isUlid($product->public_id));
        $this->assertNotSame((string) $category->id, $category->public_id);
        $this->assertNotSame((string) $product->id, $product->public_id);
    }
}
