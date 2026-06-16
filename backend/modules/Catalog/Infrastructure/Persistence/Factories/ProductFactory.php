<?php

declare(strict_types=1);

namespace Modules\Catalog\Infrastructure\Persistence\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Catalog\Domain\Enums\ProductStatus;
use Modules\Catalog\Domain\Models\Category;
use Modules\Catalog\Domain\Models\Product;

/**
 * @extends Factory<Product>
 */
final class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'public_id' => (string) Str::ulid(),
            'category_id' => Category::factory(),
            'name' => Str::title($name),
            'slug' => Str::slug($name),
            'sku' => strtoupper(fake()->unique()->bothify('PRD-####')),
            'short_description' => fake()->optional()->sentence(),
            'status' => ProductStatus::Draft,
            'position' => fake()->numberBetween(0, 100),
        ];
    }

    public function active(): self
    {
        return $this->state([
            'status' => ProductStatus::Active,
        ]);
    }

    public function archived(): self
    {
        return $this->state([
            'status' => ProductStatus::Archived,
        ]);
    }
}
