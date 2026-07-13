<?php

declare(strict_types=1);

namespace Modules\Pricing\Domain\Exceptions;

use RuntimeException;

final class ProductPriceNotConfiguredException extends RuntimeException
{
    public static function forProduct(string $publicId): self
    {
        return new self(sprintf('Product [%s] has no base price configured.', $publicId));
    }
}
