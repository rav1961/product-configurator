<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\SavedConfiguration\Presentation\Http\Controllers\SavedConfigurationShowController;
use Modules\SavedConfiguration\Presentation\Http\Controllers\SavedConfigurationStoreController;
use Modules\Shared\Presentation\Http\ApiRouteMiddleware;

Route::middleware(ApiRouteMiddleware::VERIFIED)
    ->group(function () {
        Route::post(
            '/saved-configuration',
            SavedConfigurationStoreController::class,
        )->name('api.saved-configuration.store');

        Route::get(
            '/saved-configuration/{savedConfigurationId}',
            SavedConfigurationShowController::class,
        )->name('api.saved-configuration.show');
    });
