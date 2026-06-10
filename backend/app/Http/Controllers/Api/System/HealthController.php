<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\System;

use App\Actions\System\GetHealthStatusAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

final class HealthController extends Controller
{
    public function __invoke(
        GetHealthStatusAction $action,
    ): JsonResponse {
        return response()->json([
            'data' => $action->execute()->toArray(),
        ]);
    }
}
