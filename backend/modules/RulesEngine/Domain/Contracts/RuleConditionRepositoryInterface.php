<?php

declare(strict_types=1);

namespace Modules\RulesEngine\Domain\Contracts;

use Illuminate\Support\Collection;
use Modules\RulesEngine\Domain\Models\RuleCondition;

interface RuleConditionRepositoryInterface
{
    /**
     * @return Collection<int, RuleCondition>
     */
    public function listOrderedForGroup(int $ruleGroupId): Collection;
}
