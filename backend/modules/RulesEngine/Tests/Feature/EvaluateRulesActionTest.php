<?php

declare(strict_types=1);

namespace Modules\RulesEngine\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Configurator\Domain\ValueObjects\ConfigurationSelection;
use Modules\RulesEngine\Application\Actions\EvaluateRulesAction;
use Modules\RulesEngine\Domain\Enums\RuleActionType;
use Modules\RulesEngine\Domain\Models\Rule;
use Modules\RulesEngine\Domain\Models\RuleAction;
use Modules\RulesEngine\Domain\Models\RuleCondition;
use Modules\RulesEngine\Domain\Models\RuleGroup;
use Modules\RulesEngine\Tests\Concerns\BuildsRulesFixtures;
use Modules\Shared\Domain\Enums\SelectionCondition;
use Tests\TestCase;

final class EvaluateRulesActionTest extends TestCase
{
    use BuildsRulesFixtures;
    use RefreshDatabase;

    public function test_execute_loads_active_rules_from_database(): void
    {
        ['product' => $product, 'attribute' => $color] = $this->productWithAttribute();
        $rule = Rule::factory()->for($product)->create(['position' => 1, 'is_active' => true]);
        $group = RuleGroup::factory()->for($rule)->create();

        RuleCondition::factory()->for($group)->create([
            'source_attribute_id' => $color->id,
            'condition' => SelectionCondition::Equals,
            'condition_value' => 'red',
        ]);
        RuleAction::factory()->for($rule)->create([
            'type' => RuleActionType::AddModifier,
            'payload' => ['amount' => '199.99', 'label' => 'Glass'],
        ]);
        Rule::factory()->for($product)->inactive()->create();

        $result = app(EvaluateRulesAction::class)->execute(
            $product->public_id,
            ConfigurationSelection::fromArray([$color->public_id => 'red']),
        );

        $this->assertSame($product->public_id, $result->productId);
        $this->assertCount(1, $result->matchedRules);
        $this->assertSame('199.99', $result->effects->modifiers[0]->amount);
    }

    public function test_execute_returns_empty_when_no_rules_match(): void
    {
        ['product' => $product, 'attribute' => $color] = $this->productWithAttribute();
        $rule = Rule::factory()->for($product)->create();
        $group = RuleGroup::factory()->for($rule)->create();

        RuleCondition::factory()->for($group)->create([
            'source_attribute_id' => $color->id,
            'condition' => SelectionCondition::Equals,
            'condition_value' => 'red',
        ]);

        $result = app(EvaluateRulesAction::class)->execute(
            $product->public_id,
            ConfigurationSelection::fromArray([$color->public_id => 'blue']),
        );

        $this->assertSame([], $result->matchedRules);
        $this->assertSame([], $result->effects->modifiers);
    }
}
