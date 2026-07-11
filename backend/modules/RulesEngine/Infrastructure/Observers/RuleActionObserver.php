<?php

declare(strict_types=1);

namespace Modules\RulesEngine\Infrastructure\Observers;

use Modules\RulesEngine\Domain\Models\RuleAction;
use Modules\RulesEngine\Domain\Validation\RuleActionPayloadValidator;

final readonly class RuleActionObserver
{
    public function __construct(
        private RuleActionPayloadValidator $validator,
    ) {}

    public function saving(RuleAction $action): void
    {
        if (! $action->isDirty(['type', 'payload'])) {
            return;
        }

        $this->validator->validate($action->type, $action->payload);
    }
}
