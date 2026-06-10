<?php

use App\Http\Controllers\Api\Catalog\ProductIndexController;
use App\Http\Controllers\Api\System\HealthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/health', HealthController::class)
    ->name('api.health');

Route::get('/catalog/products', ProductIndexController::class)
    ->name('api.catalog.products.index');

Route::get('/user', fn (Request $request) => $request->user())
    ->middleware('auth:sanctum');
