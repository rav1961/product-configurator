<?php

declare(strict_types=1);

namespace Modules\Shared\Domain\Enums;

enum SelectionCondition: string
{
    case Equals = 'equals';
    case NotEquals = 'not_equals';
    case IsSet = 'is_set';
    case IsEmpty = 'is_empty';
    case IsNotSet = 'is_not_set';

    public function label(): string
    {
        return match ($this) {
            self::Equals => __('shared.selection_condition.equals'),
            self::NotEquals => __('shared.selection_condition.not_equals'),
            self::IsEmpty => __('shared.selection_condition.is_empty'),
            self::IsSet => __('shared.selection_condition.is_set'),
            self::IsNotSet => __('shared.selection_condition.is_not_set'),
        };
    }

    public function requiredValue(): bool
    {
        return match ($this) {
            self::Equals, self::NotEquals => true,
            self::IsSet, self::IsEmpty, self::IsNotSet => false,
        };
    }
}
