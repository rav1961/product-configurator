<?php

declare(strict_types=1);

namespace Feature;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Catalog\Application\Actions\GetProductAction;
use Modules\Catalog\Domain\Models\Product;
use Tests\TestCase;

final class GetProductActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_execute_returns_active_product_with_category(): void
    {
        $product = Product::factory()->active()->create();

        $result = app(GetProductAction::class)->execute($product->public_id);

        $this->assertTrue($result->relationLoaded('category'));
        $this->assertSame($product->public_id, $result->public_id);
        $this->assertTrue($result->isActive());
    }

    public function test_execute_throws_for_inactive_product(): void
    {
        $product = Product::factory()->create();

        $this->expectException(ModelNotFoundException::class);

        app(GetProductAction::class)->execute($product->public_id);
    }

    public function test_execute_throws_for_unknown_public_id(): void
    {
        $this->expectException(ModelNotFoundException::class);

        app(GetProductAction::class)->execute((string) Str::ulid());
    }
}
