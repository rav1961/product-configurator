<?php

declare(strict_types=1);

namespace Modules\Shared\Domain\ValueObjects;

use Modules\Shared\Domain\Exceptions\InvalidMoneyException;

final readonly class Money
{
    public const PAYLOAD_KEY = 'amount';

    private function __construct(public int $amountMinor)
    {
        if ($amountMinor < 0) {
            throw InvalidMoneyException::negativeAmount();
        }
    }

    public static function pln(int $amountMinor): self
    {
        return new self($amountMinor);
    }

    public static function fromUserInput(string $input): self
    {
        return self::fromDecimal(self::normalizeUserInput($input));
    }

    public static function fromDecimal(string $decimal): self
    {
        $normalized = trim($decimal);

        if ($normalized === '' || ! preg_match('/^\d+(\.\d{1,2})?$/', $normalized)) {
            throw InvalidMoneyException::invalidDecimal($decimal);
        }

        [$whole, $fraction] = array_pad(explode('.', $normalized, 2), 2, '0');
        $fraction = str_pad(substr($fraction, 0, 2), 2, '0');

        return new self(((int) $whole * 100) + (int) $fraction);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromPayloadAmount(array $payload): self
    {
        if (! array_key_exists(self::PAYLOAD_KEY, $payload)) {
            throw InvalidMoneyException::missingPayloadAmount();
        }

        $raw = $payload[self::PAYLOAD_KEY];

        if (is_int($raw)) {
            return new self($raw);
        }

        if (is_string($raw) && is_numeric($raw)) {
            return self::fromDecimal($raw);
        }

        throw InvalidMoneyException::invalidPayloadAmount();
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function tryFromPayloadAmount(array $payload): ?self
    {
        try {
            return self::fromPayloadAmount($payload);
        } catch (InvalidMoneyException) {
            return null;
        }
    }

    /**
     * @return array{amount: int}
     */
    public function toPayloadAmount(): array
    {
        return [self::PAYLOAD_KEY => $this->amountMinor];
    }

    /**
     * @return array{amountMinor: int, amount: string}
     */
    public function toApiFields(): array
    {
        return [
            'amountMinor' => $this->amountMinor,
            'amount' => $this->toDecimal(),
        ];
    }

    public function toDecimal(): string
    {
        $whole = intdiv($this->amountMinor, 100);
        $fraction = $this->amountMinor % 100;

        return sprintf('%d.%02d', $whole, $fraction);
    }

    public static function userInputPattern(): string
    {
        return '^\d+([.,]\d{1,2})?$';
    }

    private static function normalizeUserInput(string $raw): string
    {
        $withoutSpaces = str_replace(' ', '', trim($raw));
        $lastComma = strrpos($withoutSpaces, ',');
        $lastDot = strrpos($withoutSpaces, '.');

        if ($lastComma !== false && ($lastDot === false || $lastComma > $lastDot)) {
            return str_replace(',', '.', str_replace('.', '', $withoutSpaces));
        }

        return $withoutSpaces;
    }
}
