<?php

declare(strict_types=1);

namespace Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Configurator\Domain\ValueObjects\ConfigurationSelection;
use Modules\Pricing\Application\Actions\CalculatePriceAction;
use Modules\Pricing\Domain\Models\ProductPrice;
use Modules\RulesEngine\Domain\Enums\RuleActionType;
use Modules\RulesEngine\Domain\Models\Rule;
use Modules\RulesEngine\Domain\Models\RuleAction;
use Modules\RulesEngine\Domain\Models\RuleCondition;
use Modules\RulesEngine\Domain\Models\RuleGroup;
use Modules\RulesEngine\Tests\Concerns\BuildsRulesFixtures;
use Modules\Shared\Domain\Enums\SelectionCondition;
use Tests\TestCase;

final class CalculatePriceActionTest extends TestCase
{
    use BuildsRulesFixtures;
    use RefreshDatabase;

    public function test_returns_price_and_the_evaluation_used_to_calculate_it(): void
    {
        ['product' => $product, 'attribute' => $attribute] =
            $this->productWithAttribute();

        ProductPrice::factory()
            ->for($product)
            ->create([
                'amount' => 199900,
            ]);

        $rule = Rule::factory()
            ->for($product)
            ->create();
        $group = RuleGroup::factory()
            ->for($rule)
            ->create();

        RuleCondition::factory()
            ->for($group)
            ->create([
                'source_attribute_id' => $attribute->id,
                'condition' => SelectionCondition::Equals,
                'condition_value' => 'red',
            ]);
        RuleAction::factory()
            ->for($rule)
            ->create([
                'type' => RuleActionType::AddModifier,
                'payload' => [
                    'amount' => 19999,
                    'operation' => 'add',
                    'label' => 'Red finish',
                ],
            ]);

        $result = app(CalculatePriceAction::class)
            ->executeWithEvaluation(
                $product->public_id,
                ConfigurationSelection::fromArray([
                    $attribute->public_id => 'red',
                ]),
            );

        $this->assertSame(
            $product->public_id,
            $result->price->productId,
        );
        $this->assertSame(199900, $result->price->basePrice);
        $this->assertSame(219899, $result->price->total);
        $this->assertFalse($result->price->hasOverride);
        $this->assertCount(
            1,
            $result->evaluation->effects->modifiers,
        );
        $this->assertSame(
            19999,
            $result->evaluation->effects->modifiers[0]->amountMinor,
        );
        $this->assertSame(
            'Red finish',
            $result->evaluation->effects->modifiers[0]->label,
        );
    }

    public function test_execute_still_returns_only_price_data(): void
    {
        $product = $this->ruleProduct();

        ProductPrice::factory()
            ->for($product)
            ->create([
                'amount' => 199900,
            ]);

        $result = app(CalculatePriceAction::class)->execute(
            $product->public_id,
            ConfigurationSelection::fromArray([]),
        );

        $this->assertSame($product->public_id, $result->productId);
        $this->assertSame(199900, $result->basePrice);
        $this->assertSame(199900, $result->total);
        $this->assertFalse($result->hasOverride);
    }
}
