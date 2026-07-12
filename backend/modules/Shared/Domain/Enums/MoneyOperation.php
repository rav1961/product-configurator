<?php

declare(strict_types=1);

namespace Modules\Shared\Domain\Enums;

enum MoneyOperation: string
{
    case Add = 'add';
    case Subtract = 'subtract';

    public function label(): string
    {
        return match ($this) {
            self::Add => __('shared.money.operation.add'),
            self::Subtract => __('shared.money.operation.subtract'),
        };
    }

    public function prefix(): string
    {
        return match ($this) {
            self::Add => '+',
            self::Subtract => '−',
        };
    }

    public function signedMinor(int $amountMinor): int
    {
        return match ($this) {
            self::Add => $amountMinor,
            self::Subtract => -$amountMinor,
        };
    }
}
