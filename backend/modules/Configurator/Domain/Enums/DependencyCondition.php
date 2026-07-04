<?php

declare(strict_types=1);

namespace Modules\Configurator\Domain\Enums;

enum DependencyCondition: string
{
    case Equals = 'equals';
    case NotEquals = 'not_equals';
    case IsSet = 'is_set';
    case IsEmpty = 'is_empty';
    case IsNotSet = 'is_not_set';

    public function label(): string
    {
        return match ($this) {
            self::Equals => __('configurator.dependency_condition.equals'),
            self::NotEquals => __('configurator.dependency_condition.not_equals'),
            self::IsEmpty => __('configurator.dependency_condition.is_empty'),
            self::IsSet => __('configurator.dependency_condition.is_set'),
            self::IsNotSet => __('configurator.dependency_condition.is_not_set'),
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
