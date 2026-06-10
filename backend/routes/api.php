<?php

use App\Http\Controllers\Api\System\HealthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/health', HealthController::class)
    ->name('api.health');

Route::get('/user', fn (Request $request) => $request->user())
    ->middleware('auth:sanctum');
