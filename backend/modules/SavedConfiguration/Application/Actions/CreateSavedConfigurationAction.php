<?php

declare(strict_types=1);

namespace Modules\SavedConfiguration\Application\Actions;

use Modules\Catalog\Application\Actions\GetConfigurableProductAction;
use Modules\Configurator\Domain\ValueObjects\ConfigurationSelection;
use Modules\Pricing\Application\Actions\CalculatePriceAction;
use Modules\RulesEngine\Application\Actions\EvaluateRulesAction;
use Modules\SavedConfiguration\Application\DTO\SavedConfigurationData;
use Modules\SavedConfiguration\Domain\Contracts\SavedConfigurationRepositoryInterface;
use Modules\Users\Domain\Models\User;

final readonly class CreateSavedConfigurationAction
{
    public function __construct(
        private GetConfigurableProductAction $getConfigurableProduct,
        private CalculatePriceAction $calculatePrice,
        private EvaluateRulesAction $evaluateRules,
        private SavedConfigurationRepositoryInterface $savedConfigurationRepository,
    ) {}

    public function execute(
        User $user,
        string $productPublicId,
        ConfigurationSelection $selection,
    ): SavedConfigurationData {
        $this->getConfigurableProduct->execute($productPublicId);

        $price = $this->calculatePrice->execute($productPublicId, $selection);
        $evaluation = $this->evaluateRules->execute($productPublicId, $selection);

        $savedConfiguration = $this->savedConfigurationRepository->create(
            user: $user,
            productPublicId: $productPublicId,
            selection: $selection->all(),
            price: $price->toResponseArray(),
            effects: $evaluation->toResponseArray()['effects'],
        );

        return SavedConfigurationData::fromModel(
            $savedConfiguration->load('product')
        );
    }
}
