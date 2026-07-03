<?php

declare(strict_types=1);

namespace Modules\Configurator\Domain\Exceptions;

use Modules\Shared\Domain\Exceptions\DomainRuntimeException;

final class InvalidDependencyScopeException extends DomainRuntimeException
{
    public static function attributesMustBelongsToProduct(): self
    {
        return new self(
            'Dependency source and target attributes must belong to the configured product.'
        );
    }

    public static function conditionValueRequired(): self
    {
        return new self('Dependency condition value is required for this operator.');
    }
}
