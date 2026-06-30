<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Catalog\Domain\Models\Product;
use Modules\Users\Domain\Models\User;
use Tests\TestCase;

final class ApiResponseShapeTest extends TestCase
{
    use RefreshDatabase;

    public function test_health_endpoint_uses_data_envelope(): void
    {
        $this->getJson('/api/health')
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'status',
                    'app',
                    'environment',
                    'timestamp',
                ],
            ]);
    }

    public function test_categories_endpoint_uses_data_envelope(): void
    {
        $this->actingAs(User::factory()->create())
            ->getJson('/api/categories')
            ->assertOk()
            ->assertJsonStructure([
                'data',
            ]);
    }

    public function test_products_index_uses_data_envelope_with_pagination(): void
    {
        Product::factory()->active()->create();

        $this->actingAs(User::factory()->create())
            ->getJson('/api/products')
            ->assertOk()
            ->assertJsonStructure([
                'data',
                'links',
                'meta',
            ]);
    }

    public function test_product_show_uses_data_envelope(): void
    {
        $product = Product::factory()->active()->create();

        $this->actingAs(User::factory()->create())
            ->getJson('/api/products/'.$product->public_id)
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'slug',
                    'sku',
                    'description',
                    'status',
                    'position',
                    'category',
                ],
            ]);
    }
}
