<?php

declare(strict_types=1);

namespace Modules\Configurator\Infrastructure\Providers;

use Modules\Shared\Infrastructure\Providers\ModuleServiceProvider;

final class ConfiguratorServiceProvider extends ModuleServiceProvider
{
    protected function modulePath(): string
    {
        return dirname(__DIR__, 2);
    }
}
