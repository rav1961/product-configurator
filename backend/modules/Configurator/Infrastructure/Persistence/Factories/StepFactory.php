<?php

declare(strict_types=1);

namespace Modules\Configurator\Infrastructure\Persistence\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Catalog\Domain\Models\Product;
use Modules\Configurator\Domain\Models\Step;

/**
 * @extends Factory<Step>
 */
final class StepFactory extends Factory
{
    protected $model = Step::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'public_id' => (string) Str::ulid(),
            'product_id' => Product::factory(),
            'name' => Str::title(fake()->unique()->words(2, true)),
            'position' => fake()->numberBetween(0, 100),
        ];
    }
}
