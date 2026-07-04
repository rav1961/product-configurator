<?php

declare(strict_types=1);

namespace Modules\Configurator\Domain\Enums;

enum DependencyCondition: string
{
    case Equals = 'equals';
    case NotEquals = 'not_equals';
    case IsSet = 'is_set';
    case IsEmpty = 'is_empty';

    public function label(): string
    {
        return match ($this) {
            self::Equals => __('configurator.dependency_condition.equals'),
            self::NotEquals => __('configurator.dependencies_condition.not_equals'),
            self::IsEmpty => __('configurator.dependencies_condition.is_empty'),
            self::IsSet => __('configurator.dependencies_condition.is_set'),
        };
    }

    public function requiredValue(): bool
    {
        return match ($this) {
            self::Equals, self::NotEquals => true,
            self::IsSet, self::IsEmpty => false,
        };
    }
}
