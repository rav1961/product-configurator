<?php

declare(strict_types=1);

namespace Modules\Pricing\Domain\ValueObjects;

final readonly class PricingResult
{
    public function __construct(
        public int $total,
        public bool $hasOverride,
    ) {}
}
