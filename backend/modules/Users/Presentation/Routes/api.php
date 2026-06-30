<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Users\Presentation\Http\Controllers\LoginController;
use Modules\Users\Presentation\Http\Controllers\LogoutController;
use Modules\Users\Presentation\Http\Controllers\ProfileController;
use Modules\Users\Presentation\Http\Controllers\RegisterController;
use Modules\Users\Presentation\Http\Controllers\SendVerificationEmailController;
use Modules\Users\Presentation\Http\Controllers\VerifyEmailController;

Route::post('/register', RegisterController::class)->name('api.register');
Route::post('/login', LoginController::class)->name('api.login');

Route::get('/email/verify/{id}/{hash}', VerifyEmailController::class)
    ->middleware(['signed', 'throttle:6,1'])
    ->name('api.verification.verify');

Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('/logout', LogoutController::class)->name('api.logout');
    Route::get('/profile', ProfileController::class)->name('api.profile');

    Route::post('/email/verification', SendVerificationEmailController::class)
        ->middleware('throttle:6,1')
        ->name('api.verification.send');
});
