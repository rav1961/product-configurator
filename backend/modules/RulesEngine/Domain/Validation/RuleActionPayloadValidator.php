<?php

declare(strict_types=1);

namespace Modules\RulesEngine\Domain\Validation;

use Modules\RulesEngine\Domain\Enums\RuleActionType;
use Modules\RulesEngine\Domain\Exceptions\InvalidRuleScopeException;
use Modules\Shared\Domain\Exceptions\InvalidMoneyException;
use Modules\Shared\Domain\ValueObjects\Money;
use Modules\Shared\Domain\ValueObjects\MoneyAdjustment;

final readonly class RuleActionPayloadValidator
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function validate(RuleActionType $type, array $payload): void
    {
        match ($type) {
            RuleActionType::AddModifier => $this->validateAddModifier($payload),
            RuleActionType::SetOverride => $this->validateSetOverride($payload),
            RuleActionType::ExcludeOption => $this->validateExcludeOption($payload),
            RuleActionType::AddMessage => $this->validateAddMessage($payload),
        };
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function validateAddModifier(array $payload): void
    {
        $this->parseMoneyAdjustment($payload);

        if (array_key_exists('label', $payload) && ! is_string($payload['label'])) {
            throw InvalidRuleScopeException::invalidActionPayload('label must be a string.');
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function validateSetOverride(array $payload): void
    {
        $this->parseMoneyAmount($payload);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function validateExcludeOption(array $payload): void
    {
        if (! isset($payload['attribute_id']) || ! is_string($payload['attribute_id']) || $payload['attribute_id'] === '') {
            throw InvalidRuleScopeException::invalidActionPayload('attribute_id is required.');
        }
        if (! isset($payload['value']) || ! is_string($payload['value']) || $payload['value'] === '') {
            throw InvalidRuleScopeException::invalidActionPayload('value is required.');
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function validateAddMessage(array $payload): void
    {
        if (! isset($payload['level']) || ! in_array($payload['level'], ['info', 'warning', 'error'], true)) {
            throw InvalidRuleScopeException::invalidActionPayload('level must be info, warning, or error.');
        }
        if (! isset($payload['message']) || ! is_string($payload['message']) || $payload['message'] === '') {
            throw InvalidRuleScopeException::invalidActionPayload('message is required.');
        }
        if (strlen($payload['message']) > 500) {
            throw InvalidRuleScopeException::invalidActionPayload('message must not exceed 500 characters.');
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function parseMoneyAmount(array $payload): void
    {
        try {
            Money::fromPayloadAmount($payload);
        } catch (InvalidMoneyException $e) {
            throw InvalidRuleScopeException::invalidActionPayload($e->getMessage());
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function parseMoneyAdjustment(array $payload): void
    {
        try {
            MoneyAdjustment::fromPayload($payload);
        } catch (InvalidMoneyException $e) {
            throw InvalidRuleScopeException::invalidActionPayload($e->getMessage());
        }
    }
}
