<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\RulesEngine\Presentation\Http\Controllers\RulesEvaluateController;
use Modules\Shared\Presentation\Http\ApiRouteMiddleware;

Route::middleware(ApiRouteMiddleware::VERIFIED)
    ->group(function (): void {
        Route::post('/products/{productId}/rules/evaluate', RulesEvaluateController::class)
            ->name('api.products.rules.evaluate');
    });
