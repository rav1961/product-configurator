<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\System\Presentation\Http\Controllers\HealthController;

Route::get('/health', HealthController::class)
    ->name('api.health');
