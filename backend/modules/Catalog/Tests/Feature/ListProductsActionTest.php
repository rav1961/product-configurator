<?php

declare(strict_types=1);

namespace Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Catalog\Application\Actions\ListProductsAction;
use Modules\Catalog\Domain\Models\Category;
use Modules\Catalog\Domain\Models\Product;
use Tests\TestCase;

final class ListProductsActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_execute_returns_only_active_products(): void
    {
        Product::factory()->active()->count(2)->create();
        Product::factory()->create();

        $result = app(ListProductsAction::class)->execute();

        $this->assertCount(2, $result->items());
    }

    public function test_execute_filters_by_active_category_public_id(): void
    {
        $category = Category::factory()->create();

        Product::factory()->active()->for($category)->create();
        Product::factory()->active()->create();

        $result = app(ListProductsAction::class)->execute(
            categoryPublicId: $category->public_id,
        );

        $this->assertCount(1, $result->items());
        $this->assertSame($category->public_id, $result->items()[0]->category?->public_id);
    }

    public function test_execute_excludes_products_from_inactive_category_even_when_public_id_matches(): void
    {
        $category = Category::factory()->inactive()->create();

        Product::factory()->active()->for($category)->create();

        $result = app(ListProductsAction::class)->execute(
            categoryPublicId: $category->public_id,
        );

        $this->assertCount(0, $result->items());
    }

    public function test_execute_respects_per_page(): void
    {
        Product::factory()->active()->count(3)->create();

        $result = app(ListProductsAction::class)->execute(perPage: 2);

        $this->assertCount(2, $result->items());
        $this->assertSame(3, $result->total());
    }
}
