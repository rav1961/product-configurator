<?php

declare(strict_types=1);

namespace Modules\Shared\Domain\Exceptions;

final class InvalidMoneyException extends DomainRuntimeException
{
    public static function invalidDecimal(string $decimal): self
    {
        return new self(sprintf('Invalid monetary decimal: "%s".', $decimal));
    }

    public static function negativeAmount(): self
    {
        return new self('Money amount cannot be negative.');
    }

    public static function missingPayloadAmount(): self
    {
        return new self('Payload must contain a valid amount.');
    }

    public static function invalidPayloadAmount(): self
    {
        return new self('amount must be a non-negative integer (minor units) or a legacy decimal string.');
    }
}
