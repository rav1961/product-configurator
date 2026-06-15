<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use Modules\Shared\Providers\SharedServiceProvider;

return [
    AppServiceProvider::class,
    AdminPanelProvider::class,
    SharedServiceProvider::class,
];
