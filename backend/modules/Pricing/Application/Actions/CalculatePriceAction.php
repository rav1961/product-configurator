<?php

declare(strict_types=1);

namespace Modules\Pricing\Application\Actions;

use Modules\Catalog\Application\Actions\GetConfigurableProductAction;
use Modules\Configurator\Domain\ValueObjects\ConfigurationSelection;
use Modules\Pricing\Application\DTO\PriceCalculationData;
use Modules\Pricing\Domain\Contracts\ProductPriceRepositoryInterface;
use Modules\Pricing\Domain\Exceptions\ProductPriceNotConfiguredException;
use Modules\Pricing\Domain\Services\PricingCalculator;
use Modules\RulesEngine\Application\Actions\EvaluateRulesAction;

final readonly class CalculatePriceAction
{
    public function __construct(
        private GetConfigurableProductAction $getConfigurableProduct,
        private ProductPriceRepositoryInterface $productPrices,
        private EvaluateRulesAction $evaluateRules,
        private PricingCalculator $calculator,
    ) {}

    public function execute(
        string $productPublicId,
        ConfigurationSelection $selection,
    ): PriceCalculationData {
        $this->getConfigurableProduct->execute($productPublicId);

        $productPrice = $this->productPrices->findByProductPublicId($productPublicId)
            ?? throw ProductPriceNotConfiguredException::forProduct($productPublicId);

        $evaluation = $this->evaluateRules->execute($productPublicId, $selection);
        $result = $this->calculator->calculate($productPrice->amount, $evaluation->effects);

        return new PriceCalculationData(
            productId: $productPublicId,
            basePrice: $productPrice->amount,
            total: $result->total,
            hasOverride: $result->hasOverride,
        );
    }
}
