<?php

declare(strict_types=1);

namespace Modules\Configurator\Infrastructure\Persistence\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Catalog\Domain\Models\Product;
use Modules\Configurator\Domain\Models\AttributeCollection;

/**
 * @extends Factory<AttributeCollection>
 */
final class AttributeCollectionFactory extends Factory
{
    protected $model = AttributeCollection::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = Str::title(fake()->unique()->words(2, true));

        return [
            'public_id' => (string) Str::ulid(),
            'product_id' => Product::factory(),
            'name' => $name,
            'key' => Str::slug($name),
            'position' => fake()->numberBetween(0, 100),
        ];
    }
}
