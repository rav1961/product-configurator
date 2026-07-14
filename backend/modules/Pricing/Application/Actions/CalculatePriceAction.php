<?php

declare(strict_types=1);

namespace Modules\Pricing\Application\Actions;

use Modules\Configurator\Domain\ValueObjects\ConfigurationSelection;
use Modules\Pricing\Application\DTO\EvaluatedPriceData;
use Modules\Pricing\Application\DTO\PriceCalculationData;
use Modules\Pricing\Domain\Contracts\ProductPriceRepositoryInterface;
use Modules\Pricing\Domain\Exceptions\ProductPriceNotConfiguredException;
use Modules\Pricing\Domain\Services\PricingCalculator;
use Modules\RulesEngine\Application\Actions\EvaluateRulesAction;

final readonly class CalculatePriceAction
{
    public function __construct(
        private ProductPriceRepositoryInterface $productPrices,
        private EvaluateRulesAction $evaluateRules,
        private PricingCalculator $calculator,
    ) {}

    public function execute(
        string $productPublicId,
        ConfigurationSelection $selection,
    ): PriceCalculationData {
        return $this->executeWithEvaluation(
            $productPublicId,
            $selection,
        )->price;
    }

    public function executeWithEvaluation(
        string $productPublicId,
        ConfigurationSelection $selection,
    ): EvaluatedPriceData {
        $evaluation = $this->evaluateRules->execute(
            $productPublicId,
            $selection,
        );

        $productPrice = $this->productPrices
            ->findByProductPublicId($productPublicId)
            ?? throw ProductPriceNotConfiguredException::forProduct(
                $productPublicId,
            );

        $result = $this->calculator->calculate(
            $productPrice->amount,
            $evaluation->effects,
        );

        return new EvaluatedPriceData(
            price: new PriceCalculationData(
                productId: $productPublicId,
                basePrice: $productPrice->amount,
                total: $result->total,
                hasOverride: $result->hasOverride,
            ),
            evaluation: $evaluation,
        );
    }
}
