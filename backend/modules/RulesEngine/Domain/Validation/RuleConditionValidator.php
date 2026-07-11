<?php

declare(strict_types=1);

namespace Modules\RulesEngine\Domain\Validation;

use Modules\RulesEngine\Domain\Exceptions\InvalidRuleScopeException;
use Modules\RulesEngine\Domain\Models\RuleCondition;

final readonly class RuleConditionValidator
{
    public function validate(RuleCondition $condition): void
    {
        if ($condition->condition->requiredValue() && blank($condition->condition_value)) {
            throw InvalidRuleScopeException::conditionValueRequired();
        }

        if (! $condition->isDirty(['rule_group_id', 'source_attribute_id'])) {
            return;
        }

        $source = $condition->sourceAttribute()
            ->with('step')
            ->first();

        if ($source === null) {
            return;
        }

        $ruleProductId = $condition->ruleGroup()
            ->with('rule')
            ->first()
            ?->rule
            ?->product_id;

        if ($ruleProductId === null) {
            return;
        }

        if ($source->step->product_id !== $ruleProductId) {
            throw InvalidRuleScopeException::attributeMustBelongToProduct();
        }
    }
}
