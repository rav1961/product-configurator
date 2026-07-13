<?php

declare(strict_types=1);

namespace Modules\Pricing\Application\DTO;

use Spatie\LaravelData\Data;

final class PriceCalculationData extends Data
{
    public function __construct(
        public string $productId,
        public int $basePrice,
        public int $total,
        public bool $hasOverride,
    ) {}

    /**
     * @return array{
     *     productId: string,
     *     basePrice: int,
     *     total: int,
     *     hasOverride: bool
     * }
     */
    public function toResponseArray(): array
    {
        return [
            'productId' => $this->productId,
            'basePrice' => $this->basePrice,
            'total' => $this->total,
            'hasOverride' => $this->hasOverride,
        ];
    }
}
