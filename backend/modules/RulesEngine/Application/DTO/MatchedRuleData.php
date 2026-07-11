<?php

declare(strict_types=1);

namespace Modules\RulesEngine\Application\DTO;

use Spatie\LaravelData\Data;

final class MatchedRuleData extends Data
{
    public function __construct(
        public string $id,
        public string $name,
        public int $position,
    ) {}
}
