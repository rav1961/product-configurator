<?php

declare(strict_types=1);

namespace Modules\RulesEngine\Domain\Contracts;

use Illuminate\Support\Collection;
use Modules\RulesEngine\Domain\Models\RuleAction;

interface RuleActionRepositoryInterface
{
    /**
     * @return Collection<int, RuleAction>
     */
    public function listOrderedForRule(int $ruleId): Collection;
}
