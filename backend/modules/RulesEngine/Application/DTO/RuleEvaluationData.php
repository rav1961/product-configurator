<?php

declare(strict_types=1);

namespace Modules\RulesEngine\Application\DTO;

use Spatie\LaravelData\Data;

final class RuleEvaluationData extends Data
{
    /**
     * @param  array<int, MatchedRuleData>  $matchedRules
     */
    public function __construct(
        public string $productId,
        public array $matchedRules,
        public RuleEffectsData $effects,
    ) {}

    /**
     * @return array{
     *     productId: string,
     *     matchedRules: list<array<string, mixed>>,
     *     effects: array{
     *         modifiers: list<array<string, mixed>>,
     *         overrides: list<array<string, mixed>>,
     *         excludedOptions: list<array<string, mixed>>,
     *         messages: list<array<string, mixed>>
     *     }
     * }
     */
    public function toResponseArray(): array
    {
        return [
            'productId' => $this->productId,
            'matchedRules' => array_map(
                static fn (MatchedRuleData $rule): array => $rule->toArray(),
                $this->matchedRules,
            ),
            'effects' => [
                'modifiers' => array_map(
                    static fn (RuleModifierEffectData $effect): array => $effect->toResponseArray(),
                    $this->effects->modifiers,
                ),
                'overrides' => array_map(
                    static fn (RuleOverrideEffectData $effect): array => $effect->toResponseArray(),
                    $this->effects->overrides,
                ),
                'excludedOptions' => array_map(
                    static fn (RuleExcludedOptionEffectData $effect): array => $effect->toArray(),
                    $this->effects->excludedOptions,
                ),
                'messages' => array_map(
                    static fn (RuleMessageEffectData $effect): array => $effect->toArray(),
                    $this->effects->messages,
                ),
            ],
        ];
    }
}
