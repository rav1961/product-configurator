<?php

declare(strict_types=1);

namespace Modules\Configurator\Domain\Exceptions;

use Modules\Shared\Domain\Exceptions\DomainRuntimeException;

final class InvalidAttributeValueOwnershipException extends DomainRuntimeException
{
    public static function mustBelongToExactlyOneOwner(): self
    {
        return new self('Attribute value must belong to exactly one attribute or collection.');
    }
}
