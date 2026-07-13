<?php

declare(strict_types=1);

namespace Modules\Pricing\Infrastructure\Persistence\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Catalog\Domain\Models\Product;
use Modules\Pricing\Domain\Models\ProductPrice;

/**
 * @extends Factory<ProductPrice>
 */
final class ProductPriceFactory extends Factory
{
    protected $model = ProductPrice::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory()->active()->configurable(),
            'amount' => 199900,
        ];
    }
}
