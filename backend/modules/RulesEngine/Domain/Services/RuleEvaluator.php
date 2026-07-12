<?php

declare(strict_types=1);

namespace Modules\RulesEngine\Domain\Services;

use Illuminate\Support\Collection;
use Modules\Configurator\Domain\ValueObjects\ConfigurationSelection;
use Modules\RulesEngine\Application\DTO\MatchedRuleData;
use Modules\RulesEngine\Application\DTO\RuleEffectsData;
use Modules\RulesEngine\Application\DTO\RuleEvaluationData;
use Modules\RulesEngine\Application\DTO\RuleExcludedOptionEffectData;
use Modules\RulesEngine\Application\DTO\RuleMessageEffectData;
use Modules\RulesEngine\Application\DTO\RuleModifierEffectData;
use Modules\RulesEngine\Application\DTO\RuleOverrideEffectData;
use Modules\RulesEngine\Domain\Enums\MatchMode;
use Modules\RulesEngine\Domain\Enums\RuleActionType;
use Modules\RulesEngine\Domain\Exceptions\InvalidRuleScopeException;
use Modules\RulesEngine\Domain\Models\Rule;
use Modules\RulesEngine\Domain\Models\RuleAction;
use Modules\RulesEngine\Domain\Models\RuleCondition;
use Modules\RulesEngine\Domain\Models\RuleGroup;
use Modules\Shared\Domain\Exceptions\InvalidMoneyException;
use Modules\Shared\Domain\Services\SelectionConditionMatcher;
use Modules\Shared\Domain\ValueObjects\Money;
use Modules\Shared\Domain\ValueObjects\MoneyAdjustment;

final readonly class RuleEvaluator
{
    public function __construct(
        private SelectionConditionMatcher $matcher,
    ) {}

    /**
     * @param  Collection<int, Rule>  $rules
     */
    public function evaluate(
        string $productPublicId,
        ConfigurationSelection $selection,
        Collection $rules,
    ): RuleEvaluationData {
        $matchedRules = [];
        $effects = new RuleEffectsData;

        foreach ($rules as $rule) {
            if (! $this->ruleMatches($rule, $selection)) {
                continue;
            }

            $matchedRules[] = new MatchedRuleData(
                id: $rule->public_id,
                name: $rule->name,
                position: $rule->position,
            );

            foreach ($rule->actions as $action) {
                $this->applyAction($effects, $rule->public_id, $action);
            }
        }

        return new RuleEvaluationData(
            productId: $productPublicId,
            matchedRules: $matchedRules,
            effects: $effects,
        );
    }

    private function ruleMatches(Rule $rule, ConfigurationSelection $selection): bool
    {
        if ($rule->groups->isEmpty()) {
            return false;
        }

        $groupResults = $rule->groups
            ->map(fn (RuleGroup $group): bool => $this->groupMatches($group, $selection))
            ->all();

        return match ($rule->groups_match_mode) {
            MatchMode::All => ! in_array(false, $groupResults, true),
            MatchMode::Any => in_array(true, $groupResults, true),
        };
    }

    private function groupMatches(RuleGroup $group, ConfigurationSelection $selection): bool
    {
        if ($group->conditions->isEmpty()) {
            return false;
        }

        $conditionResults = $group->conditions
            ->map(fn (RuleCondition $condition): bool => $this->conditionMatches($condition, $selection))
            ->all();

        return match ($group->conditions_match_mode) {
            MatchMode::All => ! in_array(false, $conditionResults, true),
            MatchMode::Any => in_array(true, $conditionResults, true),
        };
    }

    private function conditionMatches(RuleCondition $condition, ConfigurationSelection $selection): bool
    {
        $sourcePublicId = $condition->sourceAttribute->public_id;

        return $this->matcher->matches(
            $selection->get($sourcePublicId),
            $condition->condition,
            $condition->condition_value,
        );
    }

    private function applyAction(RuleEffectsData $effects, string $rulePublicId, RuleAction $action): void
    {
        $payload = $action->payload;

        match ($action->type) {
            RuleActionType::AddModifier => $effects->modifiers[] = $this->modifierEffect($rulePublicId, $payload, $action->position),
            RuleActionType::SetOverride => $effects->overrides[] = $this->overrideEffect($rulePublicId, $payload, $action->position),
            RuleActionType::ExcludeOption => $effects->excludedOptions[] = new RuleExcludedOptionEffectData(
                ruleId: $rulePublicId,
                attributeId: $payload['attribute_id'],
                value: $payload['value'],
                position: $action->position,
            ),
            RuleActionType::AddMessage => $effects->messages[] = new RuleMessageEffectData(
                ruleId: $rulePublicId,
                level: $payload['level'],
                message: $payload['message'],
                position: $action->position,
            ),
        };
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function modifierEffect(string $rulePublicId, array $payload, int $position): RuleModifierEffectData
    {
        $adjustment = $this->parseAdjustment($payload);

        return new RuleModifierEffectData(
            ruleId: $rulePublicId,
            amountMinor: $adjustment->money->amountMinor,
            operation: $adjustment->operation,
            label: $payload['label'] ?? null,
            position: $position,
        );
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function overrideEffect(string $rulePublicId, array $payload, int $position): RuleOverrideEffectData
    {
        $money = $this->parseAmount($payload);

        return new RuleOverrideEffectData(
            ruleId: $rulePublicId,
            amountMinor: $money->amountMinor,
            position: $position,
        );
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function parseAmount(array $payload): Money
    {
        try {
            return Money::fromPayloadAmount($payload);
        } catch (InvalidMoneyException $e) {
            throw InvalidRuleScopeException::invalidActionPayload($e->getMessage());
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function parseAdjustment(array $payload): MoneyAdjustment
    {
        try {
            return MoneyAdjustment::fromPayload($payload);
        } catch (InvalidMoneyException $e) {
            throw InvalidRuleScopeException::invalidActionPayload($e->getMessage());
        }
    }
}
