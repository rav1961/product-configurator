<?php

declare(strict_types=1);

namespace Modules\RulesEngine\Infrastructure\Persistence\Repositories;

use Illuminate\Support\Collection;
use Modules\RulesEngine\Domain\Contracts\RuleGroupRepositoryInterface;
use Modules\RulesEngine\Domain\Models\RuleGroup;

final class EloquentRuleGroupRepository implements RuleGroupRepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function listOrderedForRule(int $ruleId): Collection
    {
        return RuleGroup::query()
            ->where('rule_id', $ruleId)
            ->ordered()
            ->get();
    }
}
