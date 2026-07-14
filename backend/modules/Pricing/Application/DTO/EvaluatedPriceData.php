<?php

declare(strict_types=1);

namespace Modules\Pricing\Application\DTO;

use Modules\RulesEngine\Application\DTO\RuleEvaluationData;
use Spatie\LaravelData\Data;

final class EvaluatedPriceData extends Data
{
    public function __construct(
        public PriceCalculationData $price,
        public RuleEvaluationData $evaluation,
    ) {}
}
