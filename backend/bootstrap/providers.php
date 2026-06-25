<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use Modules\Catalog\Infrastructure\Providers\CatalogServiceProvider;
use Modules\System\Infrastructure\Providers\SystemServiceProvider;
use Modules\Users\Infrastructure\Providers\UsersServiceProvider;

return [
    AppServiceProvider::class,
    AdminPanelProvider::class,
    SystemServiceProvider::class,
    UsersServiceProvider::class,
    CatalogServiceProvider::class,
];
