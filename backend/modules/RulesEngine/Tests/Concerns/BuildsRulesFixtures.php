<?php

declare(strict_types=1);

namespace Modules\RulesEngine\Tests\Concerns;

use Illuminate\Support\Collection;
use Modules\Catalog\Domain\Models\Product;
use Modules\Configurator\Domain\Models\Attribute;
use Modules\Configurator\Domain\Models\Step;
use Modules\RulesEngine\Domain\Enums\MatchMode;
use Modules\RulesEngine\Domain\Enums\RuleActionType;
use Modules\RulesEngine\Domain\Models\Rule;
use Modules\RulesEngine\Domain\Models\RuleAction;
use Modules\RulesEngine\Domain\Models\RuleCondition;
use Modules\RulesEngine\Domain\Models\RuleGroup;
use Modules\Shared\Domain\Enums\MoneyOperation;
use Modules\Shared\Domain\Enums\SelectionCondition;

trait BuildsRulesFixtures
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    protected function ruleProduct(array $attributes = []): Product
    {
        return Product::factory()->active()->configurable()->create($attributes);
    }

    /**
     * @return array{product: Product, attribute: Attribute}
     */
    protected function productWithAttribute(?Product $product = null, string $key = 'color'): array
    {
        $product ??= $this->ruleProduct();
        $step = Step::factory()->for($product)->create();
        $attribute = Attribute::factory()->for($step)->create(['key' => $key]);

        return compact('product', 'attribute');
    }

    /**
     * @param  array<int, RuleGroup>  $groups
     * @param  array<int, RuleAction>  $actions
     */
    protected function ruleGraph(
        Product $product,
        array $groups = [],
        array $actions = [],
        MatchMode $groupsMatchMode = MatchMode::Any,
        int $position = 0,
    ): Rule {
        $rule = Rule::factory()->for($product)->create([
            'groups_match_mode' => $groupsMatchMode,
            'position' => $position,
            'is_active' => true,
        ]);

        foreach ($groups as $group) {
            $group->rule_id = $rule->id;
            $group->setRelation('rule', $rule);
        }

        foreach ($actions as $action) {
            $action->rule_id = $rule->id;
            $action->setRelation('rule', $rule);
        }

        $rule->setRelation('groups', new Collection($groups));
        $rule->setRelation('actions', new Collection($actions));

        return $rule;
    }

    protected function ruleGroup(
        Rule $rule,
        Attribute $source,
        SelectionCondition $condition,
        ?string $conditionValue = 'red',
        MatchMode $conditionsMatchMode = MatchMode::All,
        int $position = 0,
    ): RuleGroup {
        $group = RuleGroup::factory()->for($rule)->make([
            'conditions_match_mode' => $conditionsMatchMode,
            'position' => $position,
        ]);

        $ruleCondition = RuleCondition::factory()->for($group)->make([
            'source_attribute_id' => $source->id,
            'condition' => $condition,
            'condition_value' => $conditionValue,
            'position' => 0,
        ]);

        $ruleCondition->setRelation('sourceAttribute', $source);
        $group->setRelation('conditions', new Collection([$ruleCondition]));

        return $group;
    }

    protected function modifierAction(
        Rule $rule,
        int $amount = 9999,
        MoneyOperation $operation = MoneyOperation::Add,
        ?string $label = 'Dopłata',
        int $position = 0,
    ): RuleAction {
        return RuleAction::factory()->for($rule)->make([
            'type' => RuleActionType::AddModifier,
            'payload' => [
                'amount' => $amount,
                'operation' => $operation->value,
                'label' => $label,
            ],
            'position' => $position,
        ]);
    }
}
