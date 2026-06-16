<?php

declare(strict_types=1);

namespace Modules\System\Presentation\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Modules\Shared\Presentation\Http\Controllers\ApiController;
use Modules\System\Application\Actions\GetHealthStatusAction;

final class HealthController extends ApiController
{
    public function __invoke(
        GetHealthStatusAction $action,
    ): JsonResponse {
        return response()->json([
            'data' => $action->execute()->toArray(),
        ]);
    }
}
