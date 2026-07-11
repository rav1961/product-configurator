<?php

declare(strict_types=1);

namespace Modules\RulesEngine\Domain\Enums;

enum RuleActionType: string
{
    case AddModifier = 'add_modifier';
    case SetOverride = 'set_override';
    case ExcludeOption = 'exclude_option';
    case AddMessage = 'add_message';

    public function label(): string
    {
        return match ($this) {
            self::AddModifier => __('rules_engine.action_type.add_modifier'),
            self::SetOverride => __('rules_engine.action_type.set_override'),
            self::ExcludeOption => __('rules_engine.action_type.exclude_option'),
            self::AddMessage => __('rules_engine.action_type.add_message'),
        };
    }
}
