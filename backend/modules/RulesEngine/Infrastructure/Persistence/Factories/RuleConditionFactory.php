<?php

declare(strict_types=1);

namespace Modules\RulesEngine\Infrastructure\Persistence\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Configurator\Domain\Models\Attribute;
use Modules\Configurator\Domain\Models\Step;
use Modules\RulesEngine\Domain\Models\Rule;
use Modules\RulesEngine\Domain\Models\RuleCondition;
use Modules\RulesEngine\Domain\Models\RuleGroup;
use Modules\Shared\Domain\Enums\SelectionCondition;

/**
 * @extends Factory<RuleCondition>
 */
final class RuleConditionFactory extends Factory
{
    protected $model = RuleCondition::class;

    public function configure(): RuleConditionFactory
    {
        return $this->afterMaking(function (RuleCondition $condition): void {
            if (isset($condition->getAttributes()['source_attribute_id'])) {
                return;
            }

            $ruleGroupId = $condition->getAttributes()['rule_group_id'] ?? null;

            if ($ruleGroupId === null) {
                return;
            }

            $rule = Rule::query()
                ->whereKey(
                    RuleGroup::query()
                        ->whereKey($ruleGroupId)
                        ->value('rule_id'),
                )
                ->first();

            if ($rule === null) {
                return;
            }

            $step = Step::factory()->create([
                'product_id' => $rule->product_id,
            ]);

            $attribute = Attribute::factory()->for($step)->create();

            $condition->source_attribute_id = $attribute->id;
        });
    }

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'public_id' => (string) Str::ulid(),
            'rule_group_id' => RuleGroup::factory(),
            'condition' => SelectionCondition::Equals,
            'condition_value' => 'red',
            'position' => fake()->numberBetween(0, 100),
        ];
    }

    public function whenSet(): RuleConditionFactory
    {
        return $this->state([
            'condition' => SelectionCondition::IsSet,
            'condition_value' => null,
        ]);
    }
}
