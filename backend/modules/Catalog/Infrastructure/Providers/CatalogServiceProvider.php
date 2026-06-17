<?php

declare(strict_types=1);

namespace Modules\Catalog\Infrastructure\Providers;

use Filament\Panel;
use Modules\Catalog\Presentation\Filament\Resources\CategoryResource;
use Modules\Catalog\Presentation\Filament\Resources\ProductResource;
use Modules\Shared\Infrastructure\Providers\ModuleServiceProvider;

final class CatalogServiceProvider extends ModuleServiceProvider
{
    protected function modulePath(): string
    {
        return dirname(__DIR__, 2);
    }

    public function register(): void
    {
        Panel::configureUsing(static function (Panel $panel): void {
            if ($panel->getId() !== 'admin') {
                return;
            }

            $panel->resources([
                CategoryResource::class,
                ProductResource::class,
            ]);
        });
    }
}
