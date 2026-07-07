<?php

declare(strict_types=1);

namespace Modules\Configurator\Application\Actions;

use Modules\Configurator\Application\DTO\ConfigurationEvaluationData;
use Modules\Configurator\Application\DTO\ConfiguratorSchemaData;
use Modules\Configurator\Domain\Contracts\DependencyRepositoryInterface;
use Modules\Configurator\Domain\Services\DependencyRuleEvaluator;
use Modules\Configurator\Domain\ValueObjects\ConfigurationSelection;

final readonly class EvaluateConfigurationAction
{
    public function __construct(
        private GetConfiguratorSchemaAction $getConfiguratorSchema,
        private DependencyRepositoryInterface $dependencies,
        private DependencyRuleEvaluator $evaluator,
    ) {}

    public function execute(
        string $productPublicId,
        ConfigurationSelection $selection,
    ): ConfigurationEvaluationData {
        $schema = $this->getConfiguratorSchema->execute($productPublicId);

        return $this->executeForSchema($schema, $productPublicId, $selection);
    }

    public function executeForSchema(
        ConfiguratorSchemaData $schema,
        string $productPublicId,
        ConfigurationSelection $selection,
    ): ConfigurationEvaluationData {
        $dependencies = $this->dependencies->listOrderedForProductPublicId($productPublicId);

        return $this->evaluator->evaluate($schema, $selection, $dependencies);
    }
}
