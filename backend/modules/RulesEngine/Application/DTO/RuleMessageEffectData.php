<?php

declare(strict_types=1);

namespace Modules\RulesEngine\Application\DTO;

use Spatie\LaravelData\Data;

final class RuleMessageEffectData extends Data
{
    public function __construct(
        public string $ruleId,
        public string $level,
        public string $message,
        public int $position,
    ) {}
}
