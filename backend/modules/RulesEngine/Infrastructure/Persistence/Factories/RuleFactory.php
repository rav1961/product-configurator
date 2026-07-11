<?php

declare(strict_types=1);

namespace Modules\RulesEngine\Infrastructure\Persistence\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Catalog\Domain\Models\Product;
use Modules\RulesEngine\Domain\Enums\MatchMode;
use Modules\RulesEngine\Domain\Models\Rule;

/**
 * @extends Factory<Rule>
 */
final class RuleFactory extends Factory
{
    protected $model = Rule::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'public_id' => (string) Str::ulid(),
            'product_id' => Product::factory(),
            'name' => fake()->sentence(3),
            'description' => fake()->optional()->sentence(),
            'groups_match_mode' => MatchMode::All,
            'position' => fake()->numberBetween(0, 100),
            'is_active' => true,
        ];
    }

    public function inactive(): RuleFactory
    {
        return $this->state([
            'is_active' => false,
        ]);
    }
}
