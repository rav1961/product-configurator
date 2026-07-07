<?php

declare(strict_types=1);

namespace Modules\Configurator\Presentation\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Modules\Configurator\Application\Actions\GetConfiguratorSchemaAction;
use Modules\Shared\Presentation\Http\Controllers\ApiController;
use Symfony\Component\HttpFoundation\Response;

final class ConfiguratorSchemaShowController extends ApiController
{
    public function __invoke(
        string $productId,
        GetConfiguratorSchemaAction $action,
    ): JsonResponse {
        return response()->json([
            'data' => $action->execute($productId)->toArray(),
        ], Response::HTTP_OK);
    }
}
