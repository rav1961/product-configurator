<?php

declare(strict_types=1);

namespace Modules\Pricing\Domain\Services;

use Modules\Pricing\Domain\ValueObjects\PricingResult;
use Modules\RulesEngine\Application\DTO\RuleEffectsData;
use Modules\RulesEngine\Application\DTO\RuleModifierEffectData;
use Modules\RulesEngine\Application\DTO\RuleOverrideEffectData;

final readonly class PricingCalculator
{
    public function calculate(int $basePrice, RuleEffectsData $effects): PricingResult
    {
        if ($effects->overrides !== []) {
            $override = $this->lastByPosition($effects->overrides);

            return new PricingResult(
                total: max(0, $override->amountMinor),
                hasOverride: true,
            );
        }

        $total = $basePrice;

        foreach ($this->sortedByPosition($effects->modifiers) as $modifier) {
            $total += $modifier->operation->signedMinor($modifier->amountMinor);
        }

        return new PricingResult(
            total: max(0, $total),
            hasOverride: false,
        );
    }

    /**
     * @param  array<int, RuleOverrideEffectData>  $overrides
     */
    private function lastByPosition(array $overrides): RuleOverrideEffectData
    {
        $sorted = $this->sortedByPosition($overrides);

        return $sorted[array_key_last($sorted)];
    }

    /**
     * @template T of RuleModifierEffectData|RuleOverrideEffectData
     *
     * @param  array<int, T>  $items
     * @return array<int, T>
     */
    private function sortedByPosition(array $items): array
    {
        $sorted = $items;
        usort(
            $sorted,
            static fn (RuleModifierEffectData|RuleOverrideEffectData $left, RuleModifierEffectData|RuleOverrideEffectData $right): int => $left->position <=> $right->position,
        );

        return $sorted;
    }
}
