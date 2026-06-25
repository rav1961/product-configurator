<?php

declare(strict_types=1);

namespace Modules\Users\Infrastructure\Providers;

use Modules\Shared\Infrastructure\Providers\ModuleServiceProvider;

final class UsersServiceProvider extends ModuleServiceProvider
{
    protected function modulePath(): string
    {
        return dirname(__DIR__, 2);
    }
}
