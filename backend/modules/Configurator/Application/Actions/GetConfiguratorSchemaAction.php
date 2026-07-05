<?php

declare(strict_types=1);

namespace Modules\Configurator\Application\Actions;

use Modules\Catalog\Application\Actions\GetConfigurableProductAction;
use Modules\Configurator\Application\DTO\ConfiguratorSchemaData;
use Modules\Configurator\Domain\Contracts\ConfiguratorGraphRepositoryInterface;

final readonly class GetConfiguratorSchemaAction
{
    public function __construct(
        private GetConfigurableProductAction $getConfigurableProduct,
        private ConfiguratorGraphRepositoryInterface $graph,
    ) {}

    public function execute(string $productPublicId): ConfiguratorSchemaData
    {
        $product = $this->getConfigurableProduct->execute($productPublicId);
        $steps = $this->graph->loadStepsForProduct($productPublicId);

        return ConfiguratorSchemaData::fromGraph($product, $steps);
    }
}
