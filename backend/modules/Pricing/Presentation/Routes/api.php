<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Pricing\Presentation\Http\Controllers\PriceCalculateController;
use Modules\Shared\Presentation\Http\ApiRouteMiddleware;

Route::middleware(ApiRouteMiddleware::VERIFIED)
    ->group(function (): void {
        Route::post('/products/{productId}/price/calculate', PriceCalculateController::class)
            ->name('api.products.price.calculate');
    });
