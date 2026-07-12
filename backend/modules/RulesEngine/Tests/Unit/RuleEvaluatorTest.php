<?php

declare(strict_types=1);

namespace Modules\RulesEngine\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Modules\Configurator\Domain\ValueObjects\ConfigurationSelection;
use Modules\RulesEngine\Domain\Enums\MatchMode;
use Modules\RulesEngine\Domain\Enums\RuleActionType;
use Modules\RulesEngine\Domain\Models\Rule;
use Modules\RulesEngine\Domain\Models\RuleAction;
use Modules\RulesEngine\Domain\Models\RuleCondition;
use Modules\RulesEngine\Domain\Models\RuleGroup;
use Modules\RulesEngine\Domain\Services\RuleEvaluator;
use Modules\RulesEngine\Tests\Concerns\BuildsRulesFixtures;
use Modules\Shared\Domain\Enums\SelectionCondition;
use Modules\Shared\Domain\Services\SelectionConditionMatcher;
use Tests\TestCase;

final class RuleEvaluatorTest extends TestCase
{
    use BuildsRulesFixtures;
    use RefreshDatabase;

    private RuleEvaluator $evaluator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->evaluator = new RuleEvaluator(new SelectionConditionMatcher);
    }

    public function test_rule_matches_when_condition_equals(): void
    {
        ['product' => $product, 'attribute' => $color] = $this->productWithAttribute();

        $group = $this->ruleGroup(
            rule: $rule = Rule::factory()->for($product)->make(),
            source: $color,
            condition: SelectionCondition::Equals,
            conditionValue: 'red',
        );

        $rule = $this->ruleGraph(
            $product,
            groups: [$group],
            actions: [
                $this->modifierAction($rule, 15000, label: 'Glass'),
            ]);

        $result = $this->evaluator->evaluate(
            $product->public_id,
            ConfigurationSelection::fromArray([$color->public_id => 'red']),
            new Collection([$rule]),
        );

        $this->assertCount(1, $result->matchedRules);
        $this->assertSame($rule->public_id, $result->matchedRules[0]->id);
        $this->assertCount(1, $result->effects->modifiers);
        $this->assertSame(15000, $result->effects->modifiers[0]->amountMinor);
        $this->assertSame('Glass', $result->effects->modifiers[0]->label);
        $this->assertSame('add', $result->effects->modifiers[0]->operation->value);
    }

    public function test_rule_does_not_match_when_condition_fails(): void
    {
        ['product' => $product, 'attribute' => $color] = $this->productWithAttribute();

        $group = $this->ruleGroup(
            rule: $rule = Rule::factory()->for($product)->make(),
            source: $color,
            condition: SelectionCondition::Equals,
            conditionValue: 'red',
        );

        $rule = $this->ruleGraph($product, groups: [$group]);

        $result = $this->evaluator->evaluate(
            $product->public_id,
            ConfigurationSelection::fromArray([$color->public_id => 'blue']),
            new Collection([$rule]),
        );

        $this->assertSame([], $result->matchedRules);
        $this->assertSame([], $result->effects->modifiers);
    }

    public function test_group_all_requires_every_condition(): void
    {
        ['product' => $product, 'attribute' => $color] = $this->productWithAttribute(key: 'color');
        ['attribute' => $size] = $this->productWithAttribute($product, 'size');
        $rule = Rule::factory()->for($product)->make();
        $group = RuleGroup::factory()->for($rule)->make([
            'conditions_match_mode' => MatchMode::All,
        ]);
        $conditionA = RuleCondition::factory()->for($group)->make([
            'source_attribute_id' => $color->id,
            'condition' => SelectionCondition::Equals,
            'condition_value' => 'red',
        ]);
        $conditionA->setRelation('sourceAttribute', $color);
        $conditionB = RuleCondition::factory()->for($group)->make([
            'source_attribute_id' => $size->id,
            'condition' => SelectionCondition::IsSet,
            'condition_value' => null,
        ]);
        $conditionB->setRelation('sourceAttribute', $size);
        $group->setRelation('conditions', new Collection([$conditionA, $conditionB]));

        $rule = $this->ruleGraph($product, groups: [$group]);

        $partial = $this->evaluator->evaluate(
            $product->public_id,
            ConfigurationSelection::fromArray([$color->public_id => 'red']),
            new Collection([$rule]),
        );

        $this->assertSame([], $partial->matchedRules);

        $full = $this->evaluator->evaluate(
            $product->public_id,
            ConfigurationSelection::fromArray([
                $color->public_id => 'red',
                $size->public_id => 120,
            ]),
            new Collection([$rule]),
        );

        $this->assertCount(1, $full->matchedRules);
    }

    public function test_group_any_matches_single_condition(): void
    {
        ['product' => $product, 'attribute' => $color] = $this->productWithAttribute();
        $group = $this->ruleGroup(
            rule: $rule = Rule::factory()->for($product)->make(),
            source: $color,
            condition: SelectionCondition::Equals,
            conditionValue: 'red',
            conditionsMatchMode: MatchMode::Any,
        );

        $rule = $this->ruleGraph($product, groups: [$group]);

        $result = $this->evaluator->evaluate(
            $product->public_id,
            ConfigurationSelection::fromArray([$color->public_id => 'red']),
            new Collection([$rule]),
        );

        $this->assertCount(1, $result->matchedRules);
    }

    public function test_rule_all_requires_every_group(): void
    {
        ['product' => $product, 'attribute' => $color] = $this->productWithAttribute(key: 'color');
        ['attribute' => $size] = $this->productWithAttribute($product, 'size');
        $rule = Rule::factory()->for($product)->make();
        $groupA = $this->ruleGroup($rule, $color, SelectionCondition::Equals, 'red');
        $groupB = $this->ruleGroup($rule, $size, SelectionCondition::IsSet, null);
        $rule = $this->ruleGraph(
            $product,
            groups: [$groupA, $groupB],
            groupsMatchMode: MatchMode::All,
        );

        $partial = $this->evaluator->evaluate(
            $product->public_id,
            ConfigurationSelection::fromArray([$color->public_id => 'red']),
            new Collection([$rule]),
        );

        $this->assertSame([], $partial->matchedRules);

        $full = $this->evaluator->evaluate(
            $product->public_id,
            ConfigurationSelection::fromArray([
                $color->public_id => 'red',
                $size->public_id => 120,
            ]),
            new Collection([$rule]),
        );

        $this->assertCount(1, $full->matchedRules);
    }

    public function test_multiple_matching_rules_aggregate_effects(): void
    {
        ['product' => $product, 'attribute' => $color] = $this->productWithAttribute();
        $ruleA = $this->ruleGraph(
            $product,
            groups: [$this->ruleGroup(Rule::factory()->for($product)->make(), $color, SelectionCondition::Equals, 'red')],
            actions: [$this->modifierAction(Rule::factory()->for($product)->make(), 5000)],
            position: 0,
        );
        $ruleB = $this->ruleGraph(
            $product,
            groups: [$this->ruleGroup(Rule::factory()->for($product)->make(), $color, SelectionCondition::IsSet, null)],
            actions: [
                RuleAction::factory()->for($ruleB = Rule::factory()->for($product)->make())->make([
                    'type' => RuleActionType::AddMessage,
                    'payload' => ['level' => 'warning', 'message' => 'Uwaga'],
                    'position' => 0,
                ]),
            ],
            position: 1,
        );

        $result = $this->evaluator->evaluate(
            $product->public_id,
            ConfigurationSelection::fromArray([$color->public_id => 'red']),
            new Collection([$ruleA, $ruleB]),
        );

        $this->assertCount(2, $result->matchedRules);
        $this->assertCount(1, $result->effects->modifiers);
        $this->assertCount(1, $result->effects->messages);
        $this->assertSame('warning', $result->effects->messages[0]->level);
    }

    public function test_rule_without_groups_never_matches(): void
    {
        ['product' => $product] = $this->productWithAttribute();

        $rule = $this->ruleGraph($product);
        $result = $this->evaluator->evaluate(
            $product->public_id,
            ConfigurationSelection::fromArray([]),
            new Collection([$rule]),
        );

        $this->assertSame([], $result->matchedRules);
    }

    public function test_maps_all_action_types(): void
    {
        ['product' => $product, 'attribute' => $color] = $this->productWithAttribute();
        $rule = Rule::factory()->for($product)->make();

        $group = $this->ruleGroup($rule, $color, SelectionCondition::IsSet, null);
        $rule = $this->ruleGraph($product, groups: [$group], actions: [
            $this->modifierAction($rule, 1000),
            RuleAction::factory()->for($rule)->make([
                'type' => RuleActionType::SetOverride,
                'payload' => ['amount' => 99900],
                'position' => 1,
            ]),
            RuleAction::factory()->for($rule)->make([
                'type' => RuleActionType::ExcludeOption,
                'payload' => ['attribute_id' => $color->public_id, 'value' => 'glass'],
                'position' => 2,
            ]),
            RuleAction::factory()->for($rule)->make([
                'type' => RuleActionType::AddMessage,
                'payload' => ['level' => 'info', 'message' => 'OK'],
                'position' => 3,
            ]),
        ]);

        $result = $this->evaluator->evaluate(
            $product->public_id,
            ConfigurationSelection::fromArray([$color->public_id => 'red']),
            new Collection([$rule]),
        );

        $this->assertCount(1, $result->effects->modifiers);
        $this->assertCount(1, $result->effects->overrides);
        $this->assertSame(99900, $result->effects->overrides[0]->amountMinor);
        $this->assertCount(1, $result->effects->excludedOptions);
        $this->assertCount(1, $result->effects->messages);
        $this->assertSame($color->public_id, $result->effects->excludedOptions[0]->attributeId);
    }
}
