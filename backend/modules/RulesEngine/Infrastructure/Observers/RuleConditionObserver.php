<?php

declare(strict_types=1);

namespace Modules\RulesEngine\Infrastructure\Observers;

use Modules\RulesEngine\Domain\Models\RuleCondition;
use Modules\RulesEngine\Domain\Validation\RuleConditionValidator;

final readonly class RuleConditionObserver
{
    public function __construct(
        private RuleConditionValidator $validator,
    ) {}

    public function saving(RuleCondition $condition): void
    {
        $this->validator->validate($condition);
    }
}
