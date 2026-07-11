<?php

declare(strict_types=1);

namespace Modules\RulesEngine\Infrastructure\Persistence\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\RulesEngine\Domain\Enums\MatchMode;
use Modules\RulesEngine\Domain\Models\Rule;
use Modules\RulesEngine\Domain\Models\RuleGroup;

/**
 * @extends Factory<RuleGroup>
 */
final class RuleGroupFactory extends Factory
{
    protected $model = RuleGroup::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'public_id' => (string) Str::ulid(),
            'rule_id' => Rule::factory(),
            'conditions_match_mode' => MatchMode::All,
            'position' => fake()->numberBetween(0, 100),
        ];
    }
}
