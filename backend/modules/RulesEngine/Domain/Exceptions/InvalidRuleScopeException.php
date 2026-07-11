<?php

declare(strict_types=1);

namespace Modules\RulesEngine\Domain\Exceptions;

use Modules\Shared\Domain\Exceptions\DomainRuntimeException;

final class InvalidRuleScopeException extends DomainRuntimeException
{
    public static function conditionValueRequired(): self
    {
        return new self('Rule condition value is required for this operator.');
    }

    public static function attributeMustBelongToProduct(): self
    {
        return new self('Rule condition source attribute must belong to the rule product.');
    }

    public static function invalidActionPayload(string $reason): self
    {
        return new self('Rule action payload is invalid: '.$reason);
    }
}
