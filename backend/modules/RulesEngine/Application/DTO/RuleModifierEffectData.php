<?php

declare(strict_types=1);

namespace Modules\RulesEngine\Application\DTO;

use Modules\Shared\Domain\Enums\MoneyOperation;
use Modules\Shared\Domain\ValueObjects\Money;
use Modules\Shared\Domain\ValueObjects\MoneyAdjustment;
use Spatie\LaravelData\Data;

final class RuleModifierEffectData extends Data
{
    public function __construct(
        public string $ruleId,
        public int $amountMinor,
        public MoneyOperation $operation,
        public ?string $label,
        public int $position,
    ) {}

    /**
     * @return array{
     *     ruleId: string,
     *     amountMinor: int,
     *     amount: string,
     *     operation: string,
     *     label: string|null,
     *     position: int
     * }
     */
    public function toResponseArray(): array
    {
        $adjustment = new MoneyAdjustment(
            money: Money::pln($this->amountMinor),
            operation: $this->operation,
        );

        return [
            'ruleId' => $this->ruleId,
            ...$adjustment->toApiFields(),
            'label' => $this->label,
            'position' => $this->position,
        ];
    }
}
