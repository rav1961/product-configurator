<?php

declare(strict_types=1);

namespace Modules\Shared\Infrastructure\Providers;

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
        $path = $this->modulePath().'/Infrastructure/Persistence/Migrations';

        if (is_dir($path)) {
            $this->loadMigrationsFrom($path);
        }
    }

    protected function bootRoutes(): void
    {
        $routes = $this->modulePath().'/Presentation/Routes/api.php';

        if (is_file($routes)) {
            Route::middleware('api')
                ->prefix('api')
                ->group($routes);
        }
    }
}
