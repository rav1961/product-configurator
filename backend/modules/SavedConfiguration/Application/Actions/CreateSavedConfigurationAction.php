<?php

declare(strict_types=1);

namespace Modules\SavedConfiguration\Application\Actions;

use Modules\Configurator\Domain\ValueObjects\ConfigurationSelection;
use Modules\Pricing\Application\Actions\CalculatePriceAction;
use Modules\SavedConfiguration\Application\DTO\SavedConfigurationData;
use Modules\SavedConfiguration\Domain\Contracts\SavedConfigurationRepositoryInterface;
use Modules\Users\Domain\Models\User;

final readonly class CreateSavedConfigurationAction
{
    public function __construct(
        private CalculatePriceAction $calculatePrice,
        private SavedConfigurationRepositoryInterface $savedConfigurations,
    ) {}

    public function execute(
        User $user,
        string $productPublicId,
        ConfigurationSelection $selection,
    ): SavedConfigurationData {
        $evaluatedPrice = $this->calculatePrice->executeWithEvaluation(
            $productPublicId,
            $selection,
        );

        $evaluationData = $evaluatedPrice->evaluation->toResponseArray();

        $savedConfiguration = $this->savedConfigurations->create(
            user: $user,
            productPublicId: $productPublicId,
            selection: $selection->all(),
            price: $evaluatedPrice->price->toResponseArray(),
            effects: $evaluationData['effects'],
        );

        return SavedConfigurationData::fromModel(
            $savedConfiguration->load('product'),
        );
    }
}
