<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Shared\Presentation\Http\ApiRouteMiddleware;
use Modules\Users\Presentation\Http\Controllers\LoginController;
use Modules\Users\Presentation\Http\Controllers\LogoutController;
use Modules\Users\Presentation\Http\Controllers\NewPasswordController;
use Modules\Users\Presentation\Http\Controllers\PasswordResetLinkController;
use Modules\Users\Presentation\Http\Controllers\ProfileController;
use Modules\Users\Presentation\Http\Controllers\RegisterController;
use Modules\Users\Presentation\Http\Controllers\SendVerificationEmailController;
use Modules\Users\Presentation\Http\Controllers\VerifyEmailController;

Route::middleware(ApiRouteMiddleware::SENSITIVE_THROTTLE)->group(function (): void {
    Route::post('/register', RegisterController::class)->name('api.register');
    Route::post('/forgot-password', PasswordResetLinkController::class)->name('api.password.forgot');
    Route::post('/reset-password', NewPasswordController::class)->name('api.password.reset');
});

Route::post('/login', LoginController::class)->name('api.login');

Route::get('/email/verify/{id}/{hash}', VerifyEmailController::class)
    ->middleware(['signed', ...ApiRouteMiddleware::SENSITIVE_THROTTLE])
    ->name('api.verification.verify');

Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('/logout', LogoutController::class)->name('api.logout');
    Route::get('/profile', ProfileController::class)->name('api.profile');

    Route::post('/email/verification', SendVerificationEmailController::class)
        ->middleware(ApiRouteMiddleware::SENSITIVE_THROTTLE)
        ->name('api.verification.send');
});
