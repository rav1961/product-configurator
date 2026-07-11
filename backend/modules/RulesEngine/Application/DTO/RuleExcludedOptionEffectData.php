<?php

declare(strict_types=1);

namespace Modules\RulesEngine\Application\DTO;

use Spatie\LaravelData\Data;

final class RuleExcludedOptionEffectData extends Data
{
    public function __construct(
        public string $ruleId,
        public string $attributeId,
        public string $value,
        public int $position,
    ) {}
}
