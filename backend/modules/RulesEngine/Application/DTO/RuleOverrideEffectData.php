<?php

declare(strict_types=1);

namespace Modules\RulesEngine\Application\DTO;

use Spatie\LaravelData\Data;

final class RuleOverrideEffectData extends Data
{
    public function __construct(
        public string $ruleId,
        public string $amount,
        public int $position,
    ) {}
}
