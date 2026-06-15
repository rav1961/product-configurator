<?php

declare(strict_types=1);

namespace Modules\Shared\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

abstract class ModuleServiceProvider extends ServiceProvider
{
    abstract protected function modulePath(): string;

    public function boot(): void
    {
        $this->bootMigrations();
        $this->bootRoutes();
    }

    protected function bootMigrations(): void
    {
        $path = $this->modulePath().'/Database/Migrations';

        if (is_dir($path)) {
            $this->loadMigrationsFrom($path);
        }
    }

    protected function bootRoutes(): void
    {
        $routes = $this->modulePath().'/Routes/api.php';

        if (is_file($routes)) {
            Route::middleware('api')
                ->prefix('api')
                ->group($routes);
        }
    }
}
