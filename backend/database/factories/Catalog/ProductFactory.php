<?php

declare(strict_types=1);

namespace Database\Factories\Catalog;

use App\Enums\Catalog\ProductStatus;
use App\Models\Catalog\Category;
use App\Models\Catalog\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

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
            'description' => fake()->optional()->paragraphs(3, true),
            'status' => ProductStatus::DRAFT,
            'position' => fake()->numberBetween(0, 100),
        ];
    }

    public function active(): self
    {
        return $this->state([
            'status' => ProductStatus::ACTIVE,
        ]);
    }

    public function archived(): self
    {
        return $this->state([
            'status' => ProductStatus::ARCHIVED,
        ]);
    }
}
