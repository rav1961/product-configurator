<?php

declare(strict_types=1);

namespace Modules\Catalog\Domain\Exceptions;

use RuntimeException;

final class ProductNotConfigurableException extends RuntimeException
{
    public static function forPublicId(string $publicId): self
    {
        return new self(
            sprintf('Product [%s] is not configurable.', $publicId)
        );
    }
}
