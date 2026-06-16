<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use Modules\Catalog\Infrastructure\Providers\CatalogServiceProvider;
use Modules\System\Infrastructure\Providers\SystemServiceProvider;

return [
    AppServiceProvider::class,
    AdminPanelProvider::class,
    SystemServiceProvider::class,
    CatalogServiceProvider::class,
];
