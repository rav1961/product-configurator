<?php

declare(strict_types=1);

namespace Modules\Shared\Domain\ValueObjects;

use Modules\Shared\Domain\Enums\MoneyOperation;
use Modules\Shared\Domain\Exceptions\InvalidMoneyException;

final readonly class MoneyAdjustment
{
    public const OPERATION_KEY = 'operation';

    public function __construct(
        public Money $money,
        public MoneyOperation $operation = MoneyOperation::Add,
    ) {}

    public static function fromUserInput(string $input, MoneyOperation $operation): self
    {
        return new self(Money::fromUserInput($input), $operation);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromPayload(array $payload): self
    {
        return new self(
            Money::fromPayloadAmount($payload),
            self::resolveOperation($payload),
        );
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function tryFromPayload(array $payload): ?self
    {
        try {
            return self::fromPayload($payload);
        } catch (InvalidMoneyException) {
            return null;
        }
    }

    /**
     * @return array{amount: int, operation: string}
     */
    public function toPayload(): array
    {
        return [
            ...$this->money->toPayloadAmount(),
            self::OPERATION_KEY => $this->operation->value,
        ];
    }

    /**
     * @return array{amountMinor: int, amount: string, operation: string}
     */
    public function toApiFields(): array
    {
        return [
            ...$this->money->toApiFields(),
            self::OPERATION_KEY => $this->operation->value,
        ];
    }

    public function signedAmountMinor(): int
    {
        return $this->operation->signedMinor($this->money->amountMinor);
    }

    public function displayPrefix(): string
    {
        return $this->operation->prefix();
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private static function resolveOperation(array $payload): MoneyOperation
    {
        if (! array_key_exists(self::OPERATION_KEY, $payload)) {
            return MoneyOperation::Add;
        }

        $raw = $payload[self::OPERATION_KEY];

        if (! is_string($raw)) {
            throw InvalidMoneyException::invalidPayloadAmount();
        }

        return MoneyOperation::tryFrom($raw)
            ?? throw InvalidMoneyException::invalidPayloadAmount();
    }
}
