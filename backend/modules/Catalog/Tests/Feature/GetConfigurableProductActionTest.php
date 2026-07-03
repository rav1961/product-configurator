<?php

declare(strict_types=1);

namespace Feature;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Catalog\Application\Actions\GetConfigurableProductAction;
use Modules\Catalog\Domain\Exceptions\ProductNotConfigurableException;
use Modules\Catalog\Domain\Models\Product;
use Tests\TestCase;

final class GetConfigurableProductActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_execute_returns_configurable_product_data(): void
    {
        $product = Product::factory()->active()->configurable()->create([
            'name' => 'Configurable Product',
            'slug' => 'configurable-product',
            'sku' => 'cp-001',
        ]);

        $result = app(GetConfigurableProductAction::class)->execute($product->public_id);

        $this->assertSame($product->public_id, $result->id);
        $this->assertSame('Configurable Product', $result->name);
        $this->assertSame('configurable-product', $result->slug);
        $this->assertSame('cp-001', $result->sku);
    }

    public function test_execute_throws_when_product_is_not_configurable(): void
    {
        $product = Product::factory()->active()->create();

        $this->expectException(ProductNotConfigurableException::class);
        $this->expectExceptionMessage(
            sprintf('Product [%s] is not configurable.', $product->public_id)
        );

        app(GetConfigurableProductAction::class)->execute($product->public_id);
    }

    public function test_execute_throws_for_inactive_product(): void
    {
        $product = Product::factory()->configurable()->create();

        $this->expectException(ModelNotFoundException::class);

        app(GetConfigurableProductAction::class)->execute($product->public_id);
    }

    public function test_execute_throws_for_unknown_public_id(): void
    {
        $this->expectException(ModelNotFoundException::class);

        app(GetConfigurableProductAction::class)->execute('unknown-public-id');
    }
}
