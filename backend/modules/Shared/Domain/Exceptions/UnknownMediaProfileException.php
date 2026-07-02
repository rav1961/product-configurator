<?php

declare(strict_types=1);

namespace Modules\Shared\Domain\Exceptions;

use RuntimeException;

final class UnknownMediaProfileException extends RuntimeException
{
    public function __construct(string $profile)
    {
        parent::__construct("Unknown media profile: {$profile}");
    }
}
