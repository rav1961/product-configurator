<?php

declare(strict_types=1);

namespace Modules\RulesEngine\Infrastructure\Persistence\Repositories;

use Illuminate\Support\Collection;
use Modules\RulesEngine\Domain\Contracts\RuleConditionRepositoryInterface;
use Modules\RulesEngine\Domain\Models\RuleCondition;

final class EloquentRuleConditionRepository implements RuleConditionRepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function listOrderedForGroup(int $ruleGroupId): Collection
    {
        return RuleCondition::query()
            ->where('rule_group_id', $ruleGroupId)
            ->ordered()
            ->get();
    }
}
