<?php

declare(strict_types=1);

namespace Modules\RulesEngine\Infrastructure\Persistence\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\RulesEngine\Domain\Enums\RuleActionType;
use Modules\RulesEngine\Domain\Models\Rule;
use Modules\RulesEngine\Domain\Models\RuleAction;
use Modules\Shared\Domain\Enums\MoneyOperation;

/**
 * @extends Factory<RuleAction>
 */
final class RuleActionFactory extends Factory
{
    protected $model = RuleAction::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'public_id' => (string) Str::ulid(),
            'rule_id' => Rule::factory(),
            'type' => RuleActionType::AddModifier,
            'payload' => [
                'amount' => 9999,
                'operation' => MoneyOperation::Add->value,
                'label' => 'Surcharge',
            ],
            'position' => fake()->numberBetween(0, 100),
        ];
    }

    public function setOverride(): static
    {
        return $this->state([
            'type' => RuleActionType::SetOverride,
            'payload' => [
                'amount' => 249900,
            ],
        ]);
    }
}
