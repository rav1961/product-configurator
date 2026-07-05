<?php

declare(strict_types=1);

namespace Modules\Configurator\Domain\Services;

use Modules\Configurator\Domain\Enums\DependencyCondition;

final readonly class DependencyConditionMatcher
{
    public function matches(
        mixed $sourceValue,
        DependencyCondition $condition,
        ?string $conditionValue,
    ): bool {
        return match ($condition) {
            DependencyCondition::Equals => $this->equals($sourceValue, $conditionValue),
            DependencyCondition::NotEquals => ! $this->equals($sourceValue, $conditionValue),
            DependencyCondition::IsSet => $this->isSet($sourceValue),
            DependencyCondition::IsEmpty => $this->isEmpty($sourceValue),
            DependencyCondition::IsNotSet => $this->isNotSet($sourceValue),
        };
    }

    private function equals(mixed $sourceValue, mixed $conditionValue): bool
    {
        if ($conditionValue === null) {
            return false;
        }

        if (is_array($sourceValue)) {
            return in_array($conditionValue, $sourceValue, true);
        }

        if ($sourceValue === null) {
            return false;
        }

        return (string) $sourceValue === $conditionValue;
    }

    private function isSet(mixed $sourceValue): bool
    {
        if ($this->isNotSet($sourceValue)) {
            return false;
        }

        if (is_array($sourceValue)) {
            return $sourceValue !== [];
        }

        if (is_bool($sourceValue) || is_int($sourceValue) || is_float($sourceValue)) {
            return true;
        }

        if (is_string($sourceValue)) {
            return $sourceValue !== '';
        }

        return true;
    }

    private function isEmpty(mixed $sourceValue): bool
    {
        if ($this->isNotSet($sourceValue)) {
            return true;
        }

        if (is_array($sourceValue)) {
            return $sourceValue === [];
        }

        if (is_string($sourceValue)) {
            return $sourceValue === '';
        }

        return false;
    }

    private function isNotSet(mixed $sourceValue): bool
    {
        return $sourceValue === null;
    }
}
