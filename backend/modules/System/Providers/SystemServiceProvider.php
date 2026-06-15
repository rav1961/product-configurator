<?php

declare(strict_types=1);

namespace Modules\System\Providers;

use Modules\Shared\Providers\ModuleServiceProvider;

final class SystemServiceProvider extends ModuleServiceProvider
{
    protected function modulePath(): string
    {
        return dirname(__DIR__);
    }
}
