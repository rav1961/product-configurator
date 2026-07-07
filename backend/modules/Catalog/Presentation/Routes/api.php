<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Catalog\Presentation\Http\Controllers\CategoryListController;
use Modules\Catalog\Presentation\Http\Controllers\ProductListController;
use Modules\Catalog\Presentation\Http\Controllers\ProductShowController;
use Modules\Shared\Presentation\Http\ApiRouteMiddleware;

Route::middleware(ApiRouteMiddleware::VERIFIED)
    ->group(function (): void {
        Route::get('/categories', CategoryListController::class)
            ->name('api.categories.list');

        Route::get('/products', ProductListController::class)
            ->name('api.products.list');

        Route::get('/products/{productId}', ProductShowController::class)
            ->name('api.products.show');
    });
