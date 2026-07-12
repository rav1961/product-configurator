<?php

declare(strict_types=1);

namespace Modules\Shared\Tests\Unit;

use Modules\Shared\Domain\Exceptions\InvalidMoneyException;
use Modules\Shared\Domain\ValueObjects\Money;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

final class MoneyTest extends TestCase
{
    public function test_creates_from_decimal_string(): void
    {
        $money = Money::fromDecimal('99.99');

        $this->assertSame(9999, $money->amountMinor);
        $this->assertSame('99.99', $money->toDecimal());
    }

    public function test_creates_from_user_input_with_polish_comma(): void
    {
        $money = Money::fromUserInput('1 234,56');

        $this->assertSame(123456, $money->amountMinor);
        $this->assertSame('1234.56', $money->toDecimal());
    }

    public function test_creates_from_minor_units(): void
    {
        $money = Money::pln(12345);

        $this->assertSame(12345, $money->amountMinor);
        $this->assertSame('123.45', $money->toDecimal());
    }

    public function test_rejects_negative_decimal(): void
    {
        $this->expectException(InvalidMoneyException::class);

        Money::fromDecimal('-10.50');
    }

    public function test_rejects_negative_minor_units(): void
    {
        $this->expectException(InvalidMoneyException::class);

        Money::pln(-100);
    }

    #[DataProvider('invalidDecimalProvider')]
    public function test_rejects_invalid_decimal(string $decimal): void
    {
        $this->expectException(InvalidMoneyException::class);

        Money::fromDecimal($decimal);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function invalidDecimalProvider(): array
    {
        return [
            'empty' => [''],
            'letters' => ['abc'],
            'too_many_fraction_digits' => ['1.234'],
        ];
    }

    public function test_from_payload_amount_reads_int(): void
    {
        $money = Money::fromPayloadAmount(['amount' => 45000]);

        $this->assertSame(45000, $money->amountMinor);
    }

    public function test_from_payload_amount_reads_legacy_decimal_string(): void
    {
        $money = Money::fromPayloadAmount(['amount' => '450.00']);

        $this->assertSame(45000, $money->amountMinor);
    }

    public function test_to_payload_and_api_fields(): void
    {
        $money = Money::pln(19999);

        $this->assertSame(['amount' => 19999], $money->toPayloadAmount());
        $this->assertSame(
            ['amountMinor' => 19999, 'amount' => '199.99'],
            $money->toApiFields(),
        );
    }

    public function test_from_payload_amount_throws_when_missing(): void
    {
        $this->expectException(InvalidMoneyException::class);

        Money::fromPayloadAmount([]);
    }
}
