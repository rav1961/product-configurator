<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Configurator\Presentation\Http\Controllers\ConfiguratorEvaluateController;
use Modules\Configurator\Presentation\Http\Controllers\ConfiguratorSchemaShowController;
use Modules\Configurator\Presentation\Http\Controllers\ConfiguratorValidateController;

Route::middleware(['auth:sanctum', 'verified'])
    ->group(function (): void {
        Route::get('/products/{productId}/configurator/schema', ConfiguratorSchemaShowController::class)
            ->name('api.products.configurator.schema');

        Route::post('/products/{productId}/configurator/evaluate', ConfiguratorEvaluateController::class)
            ->name('api.products.configurator.evaluate');

        Route::post('/products/{productId}/configurator/validate', ConfiguratorValidateController::class)
            ->name('api.products.configurator.validate');
    });
