<?php

declare(strict_types=1);

namespace Modules\RulesEngine\Infrastructure\Persistence\Repositories;

use Illuminate\Support\Collection;
use Modules\RulesEngine\Domain\Contracts\RuleActionRepositoryInterface;
use Modules\RulesEngine\Domain\Models\RuleAction;

final class EloquentRuleActionRepository implements RuleActionRepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function listOrderedForRule(int $ruleId): Collection
    {
        return RuleAction::query()
            ->where('rule_id', $ruleId)
            ->ordered()
            ->get();
    }
}
