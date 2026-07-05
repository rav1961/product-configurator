<?php

declare(strict_types=1);

namespace Modules\Configurator\Presentation\Http\Controllers;

use Modules\Configurator\Application\Actions\GetConfiguratorSchemaAction;
use Modules\Configurator\Application\DTO\ConfiguratorSchemaData;
use Modules\Shared\Presentation\Http\Controllers\ApiController;

final class ConfiguratorSchemaShowController extends ApiController
{
    public function __invoke(
        string $productId,
        GetConfiguratorSchemaAction $action
    ): ConfiguratorSchemaData {
        return $action->execute($productId);
    }
}
