<?php

declare(strict_types=1);

namespace Modules\RulesEngine\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Catalog\Domain\Models\Product;
use Modules\RulesEngine\Domain\Enums\MatchMode;
use Modules\RulesEngine\Domain\Enums\RuleActionType;
use Modules\RulesEngine\Domain\Models\Rule;
use Modules\RulesEngine\Domain\Models\RuleAction;
use Modules\RulesEngine\Domain\Models\RuleCondition;
use Modules\RulesEngine\Domain\Models\RuleGroup;
use Modules\Shared\Domain\Enums\SelectionCondition;
use Tests\TestCase;

final class RuleModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_rule_casts_enums_and_flags(): void
    {
        $rule = Rule::factory()->create([
            'groups_match_mode' => MatchMode::Any,
            'is_active' => false,
        ]);

        $this->assertSame(MatchMode::Any, $rule->groups_match_mode);
        $this->assertFalse($rule->is_active);
        $this->assertNotEmpty($rule->public_id);
    }

    public function test_ordered_scope_sorts_by_position(): void
    {
        $product = Product::factory()->create();

        Rule::factory()->for($product)->create(['position' => 5]);
        Rule::factory()->for($product)->create(['position' => 1]);

        $positions = Rule::query()
            ->where('product_id', $product->id)
            ->ordered()
            ->pluck('position')
            ->all();

        $this->assertSame([1, 5], $positions);
    }

    public function test_rule_has_groups_and_actions(): void
    {
        $rule = Rule::factory()->create();
        $group = RuleGroup::factory()->for($rule)->create();
        $action = RuleAction::factory()->for($rule)->create();

        $rule->load(['groups', 'actions']);

        $this->assertTrue($rule->groups->contains($group));
        $this->assertTrue($rule->actions->contains($action));
    }

    public function test_group_has_conditions(): void
    {
        $group = RuleGroup::factory()->create();
        $condition = RuleCondition::factory()->for($group)->create();

        $group->load('conditions');

        $this->assertTrue($group->conditions->contains($condition));
        $this->assertSame(SelectionCondition::Equals, $condition->condition);
    }

    public function test_action_casts_payload_array(): void
    {
        $action = RuleAction::factory()->create([
            'type' => RuleActionType::SetOverride,
            'payload' => ['amount' => '1000.00'],
        ]);

        $this->assertSame(RuleActionType::SetOverride, $action->type);
        $this->assertSame('1000.00', $action->payload['amount']);
    }
}
