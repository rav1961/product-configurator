<?php

declare(strict_types=1);

namespace Modules\RulesEngine\Domain\Contracts;

use Illuminate\Support\Collection;
use Modules\RulesEngine\Domain\Models\RuleGroup;

interface RuleGroupRepositoryInterface
{
    /**
     * @return Collection<int, RuleGroup>
     */
    public function listOrderedForRule(int $ruleId): Collection;
}
