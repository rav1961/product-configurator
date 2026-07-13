<?php

declare(strict_types=1);

namespace Modules\Pricing\Tests\Unit;

use Modules\Pricing\Domain\Services\PricingCalculator;
use Modules\Pricing\Domain\ValueObjects\PricingResult;
use Modules\RulesEngine\Application\DTO\RuleEffectsData;
use Modules\RulesEngine\Application\DTO\RuleModifierEffectData;
use Modules\RulesEngine\Application\DTO\RuleOverrideEffectData;
use Modules\Shared\Domain\Enums\MoneyOperation;
use Tests\TestCase;

final class PricingCalculatorTest extends TestCase
{
    private PricingCalculator $calculator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->calculator = new PricingCalculator;
    }

    public function test_returns_base_price_when_no_effects(): void
    {
        $result = $this->calculator->calculate(100_000, new RuleEffectsData);

        $this->assertInstanceOf(PricingResult::class, $result);
        $this->assertSame(100_000, $result->total);
        $this->assertFalse($result->hasOverride);
    }

    public function test_adds_positive_modifier(): void
    {
        $effects = new RuleEffectsData(modifiers: [
            new RuleModifierEffectData('01RULE', 45_000, MoneyOperation::Add, null, 0),
        ]);

        $result = $this->calculator->calculate(100_000, $effects);

        $this->assertSame(145_000, $result->total);
        $this->assertFalse($result->hasOverride);
    }

    public function test_subtracts_modifier(): void
    {
        $effects = new RuleEffectsData(modifiers: [
            new RuleModifierEffectData('01RULE', 10_000, MoneyOperation::Subtract, null, 0),
        ]);

        $result = $this->calculator->calculate(100_000, $effects);

        $this->assertSame(90_000, $result->total);
    }

    public function test_override_replaces_base_and_modifiers(): void
    {
        $effects = new RuleEffectsData(
            modifiers: [
                new RuleModifierEffectData('01RULE', 50_000, MoneyOperation::Add, null, 0),
            ],
            overrides: [
                new RuleOverrideEffectData('01RULE', 249_900, 0),
            ],
        );

        $result = $this->calculator->calculate(100_000, $effects);

        $this->assertSame(249_900, $result->total);
        $this->assertTrue($result->hasOverride);
    }

    public function test_last_override_wins_by_position(): void
    {
        $effects = new RuleEffectsData(overrides: [
            new RuleOverrideEffectData('01A', 300_000, 0),
            new RuleOverrideEffectData('01B', 199_900, 2),
            new RuleOverrideEffectData('01C', 250_000, 1),
        ]);

        $result = $this->calculator->calculate(100_000, $effects);

        $this->assertSame(199_900, $result->total);
        $this->assertTrue($result->hasOverride);
    }

    public function test_total_floors_at_zero(): void
    {
        $effects = new RuleEffectsData(modifiers: [
            new RuleModifierEffectData('01RULE', 50_000, MoneyOperation::Subtract, null, 0),
        ]);

        $result = $this->calculator->calculate(10_000, $effects);

        $this->assertSame(0, $result->total);
    }
}
