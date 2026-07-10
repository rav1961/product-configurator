<?php

declare(strict_types=1);

namespace Modules\Shared\Domain\Services;

use Modules\Shared\Domain\Enums\SelectionCondition;

final readonly class SelectionConditionMatcher
{
    public function matches(
        mixed $sourceValue,
        SelectionCondition $condition,
        ?string $conditionValue,
    ): bool {
        return match ($condition) {
            SelectionCondition::Equals => $this->equals($sourceValue, $conditionValue),
            SelectionCondition::NotEquals => ! $this->equals($sourceValue, $conditionValue),
            SelectionCondition::IsSet => $this->isSet($sourceValue),
            SelectionCondition::IsEmpty => $this->isEmpty($sourceValue),
            SelectionCondition::IsNotSet => $this->isNotSet($sourceValue),
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
