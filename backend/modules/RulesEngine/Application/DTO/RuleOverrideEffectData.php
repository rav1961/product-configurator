<?php

declare(strict_types=1);

namespace Modules\RulesEngine\Application\DTO;

use Modules\Shared\Domain\ValueObjects\Money;
use Spatie\LaravelData\Data;

final class RuleOverrideEffectData extends Data
{
    public function __construct(
        public string $ruleId,
        public int $amountMinor,
        public int $position,
    ) {}

    /**
     * @return array{
     *     ruleId: string,
     *     amountMinor: int,
     *     amount: string,
     *     position: int
     * }
     */
    public function toResponseArray(): array
    {
        return [
            'ruleId' => $this->ruleId,
            ...Money::pln($this->amountMinor)->toApiFields(),
            'position' => $this->position,
        ];
    }
}
