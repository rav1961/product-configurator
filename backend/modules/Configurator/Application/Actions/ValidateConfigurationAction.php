<?php

declare(strict_types=1);

namespace Modules\Configurator\Application\Actions;

use Modules\Configurator\Application\DTO\ConfigurationValidationResult;
use Modules\Configurator\Domain\Services\ConfigurationValidator;
use Modules\Configurator\Domain\ValueObjects\ConfigurationSelection;

final readonly class ValidateConfigurationAction
{
    public function __construct(
        private GetConfiguratorSchemaAction $getConfiguratorSchema,
        private EvaluateConfigurationAction $evaluateConfiguration,
        private ConfigurationValidator $validator,
    ) {}

    public function execute(
        string $productPublicId,
        ConfigurationSelection $selection,
    ): ConfigurationValidationResult {
        $schema = $this->getConfiguratorSchema->execute($productPublicId);
        $evaluation = $this->evaluateConfiguration->execute($productPublicId, $selection);

        return $this->validator->validate($schema, $evaluation, $selection);
    }
}
