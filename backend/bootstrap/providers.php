<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use Modules\Shared\Providers\SharedServiceProvider;
use Modules\System\Providers\SystemServiceProvider;

return [
    AppServiceProvider::class,
    AdminPanelProvider::class,
    SharedServiceProvider::class,
    SystemServiceProvider::class,
];
