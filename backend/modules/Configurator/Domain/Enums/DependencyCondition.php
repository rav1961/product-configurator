<?php

declare(strict_types=1);

namespace Modules\Configurator\Domain\Enums;

enum DependencyCondition: string
{
    case Equals = 'equals';
    case NotEquals = 'not_equals';
    case IsSet = 'is_set';
    case IsNotSet = 'is_not_set';
    case IsEmpty = 'is_empty';

    public function label(): string
    {
        return match ($this) {
            self::Equals => __('configurator.dependencies.conditions.equals'),
            self::NotEquals => __('configurator.dependencies.conditions.not_equals'),
            self::IsEmpty => __('configurator.dependencies.conditions.is_empty'),
            self::IsSet => __('configurator.dependencies.conditions.is_set'),
            self::IsNotSet => __('configurator.dependencies.conditions.is_not_set'),
        };
    }

    public function requiredValue(): bool
    {
        return match ($this) {
            self::Equals, self::NotEquals => true,
            self::IsSet, self::IsNotSet, self::IsEmpty => false,
        };
    }
}
