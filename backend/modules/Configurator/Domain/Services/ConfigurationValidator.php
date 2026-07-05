<?php

declare(strict_types=1);

namespace Modules\Configurator\Domain\Services;

use Modules\Configurator\Application\DTO\ConfigurationAttributeData;
use Modules\Configurator\Application\DTO\ConfigurationEvaluationData;
use Modules\Configurator\Application\DTO\ConfigurationOptionData;
use Modules\Configurator\Application\DTO\ConfigurationValidationResult;
use Modules\Configurator\Application\DTO\ConfiguratorSchemaData;
use Modules\Configurator\Domain\Enums\AttributeType;
use Modules\Configurator\Domain\ValueObjects\ConfigurationSelection;

final readonly class ConfigurationValidator
{
    public function validate(
        ConfiguratorSchemaData $schema,
        ConfigurationEvaluationData $evaluation,
        ConfigurationSelection $selection,
    ): ConfigurationValidationResult {
        /** @var array<string, list<string>> $errors */
        $errors = [];
        $knownKeys = [];

        foreach ($schema->allAttributes() as $attribute) {
            $knownKeys[$attribute->key] = true;
            $state = $evaluation->attributes[$attribute->key] ?? null;

            if ($state === null || ! $state->visible) {
                continue;
            }

            $value = $selection->get($attribute->key);

            if ($state->required && $this->isEmpty($value, $attribute->type)) {
                $errors[$attribute->key][] = __('configurator.validation.required', [
                    'attribute' => $attribute->name,
                ]);

                continue;
            }

            if ($this->isEmpty($value, $attribute->type)) {
                continue;
            }

            if (! $this->isValidValue($value, $attribute)) {
                $errors[$attribute->key][] = __('configurator.validation.invalid_value', [
                    'attribute' => $attribute->name,
                ]);
            }
        }

        foreach ($selection->keys() as $key) {
            if (isset($knownKeys[$key])) {
                continue;
            }

            $errors[$key][] = __('configurator.validation.unknown_attribute', [
                'attribute' => $key,
            ]);
        }

        return new ConfigurationValidationResult(
            valid: $errors === [],
            errors: $errors,
        );
    }

    private function isEmpty(mixed $value, string $type): bool
    {
        if ($value === null) {
            return true;
        }

        if ($type === AttributeType::MultiSelect->value) {
            return ! is_array($value) || $value === [];
        }

        if (is_string($value)) {
            return $value === '';
        }

        return false;
    }

    private function isValidValue(mixed $value, ConfigurationAttributeData $attribute): bool
    {
        return match (AttributeType::from($attribute->type)) {
            AttributeType::Text => $this->isValidText($value),
            AttributeType::Number => is_numeric($value),
            AttributeType::Boolean => $this->isValidBoolean($value),
            AttributeType::Select => $this->isValidSelect($value, $attribute),
            AttributeType::MultiSelect => $this->isValidMultiSelect($value, $attribute),
        };
    }

    private function isValidText(mixed $value): bool
    {
        return is_string($value) || (is_scalar($value) && ! is_bool($value));
    }

    private function isValidBoolean(mixed $value): bool
    {
        return in_array($value, [true, false, 0, 1, '0', '1'], true);
    }

    private function isValidSelect(mixed $value, ConfigurationAttributeData $attribute): bool
    {
        if (! is_string($value) && ! is_int($value)) {
            return false;
        }

        return in_array((string) $value, $this->optionValues($attribute), true);
    }

    private function isValidMultiSelect(mixed $value, ConfigurationAttributeData $attributeData): bool
    {
        if (! is_array($value)) {
            return false;
        }

        $allowed = $this->optionValues($attributeData);

        foreach ($value as $item) {
            if (! is_string($item) && ! is_int($item)) {
                return false;
            }

            if (! in_array((string) $item, $allowed, true)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return list<string>
     */
    private function optionValues(ConfigurationAttributeData $attribute): array
    {
        return array_map(
            static fn (ConfigurationOptionData $option): string => $option->value,
            $attribute->options,
        );
    }
}
