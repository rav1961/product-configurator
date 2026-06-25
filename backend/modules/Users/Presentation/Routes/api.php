<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Users\Presentation\Http\Controllers\LoginController;
use Modules\Users\Presentation\Http\Controllers\LogoutController;
use Modules\Users\Presentation\Http\Controllers\ProfileController;
use Modules\Users\Presentation\Http\Controllers\RegisterController;

Route::post('/register', RegisterController::class)->name('api.register');
Route::post('/login', LoginController::class)->name('api.login');

Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('/logout', LogoutController::class)->name('api.logout');
    Route::get('/profile', ProfileController::class)->name('api.profile');
});
