<?php

declare(strict_types=1);

namespace Modules\Shared\Tests\Unit;

use Modules\Shared\Domain\Enums\MoneyOperation;
use Modules\Shared\Domain\ValueObjects\MoneyAdjustment;
use Tests\TestCase;

final class MoneyAdjustmentTest extends TestCase
{
    public function test_from_payload_with_explicit_operation(): void
    {
        $adjustment = MoneyAdjustment::fromPayload([
            'amount' => 1000,
            'operation' => 'subtract',
        ]);

        $this->assertSame(1000, $adjustment->money->amountMinor);
        $this->assertSame(MoneyOperation::Subtract, $adjustment->operation);
        $this->assertSame(-1000, $adjustment->signedAmountMinor());
    }

    public function test_defaults_operation_to_add(): void
    {
        $adjustment = MoneyAdjustment::fromPayload(['amount' => 500]);

        $this->assertSame(MoneyOperation::Add, $adjustment->operation);
        $this->assertSame(500, $adjustment->signedAmountMinor());
    }

    public function test_to_payload_and_api_fields(): void
    {
        $adjustment = MoneyAdjustment::fromUserInput('99.99', MoneyOperation::Add);

        $this->assertSame(
            ['amount' => 9999, 'operation' => 'add'],
            $adjustment->toPayload(),
        );
        $this->assertSame(
            ['amountMinor' => 9999, 'amount' => '99.99', 'operation' => 'add'],
            $adjustment->toApiFields(),
        );
    }
}
