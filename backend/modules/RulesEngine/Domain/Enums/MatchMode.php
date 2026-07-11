<?php

declare(strict_types=1);

namespace Modules\RulesEngine\Domain\Enums;

enum MatchMode: string
{
    case All = 'all';
    case Any = 'any';

    public function label(): string
    {
        return match ($this) {
            self::All => __('rules_engine.match_mode.all'),
            self::Any => __('rules_engine.match_mode.any'),
        };
    }
}
