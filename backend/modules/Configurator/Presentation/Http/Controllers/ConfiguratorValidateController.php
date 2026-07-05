<?php

declare(strict_types=1);

namespace Modules\Configurator\Presentation\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Modules\Configurator\Application\Actions\ValidateConfigurationAction;
use Modules\Configurator\Presentation\Http\Requests\ConfigurationSelectionRequest;
use Modules\Shared\Presentation\Http\Controllers\ApiController;
use Symfony\Component\HttpFoundation\Response;

final class ConfiguratorValidateController extends ApiController
{
    public function __invoke(
        string $productId,
        ConfigurationSelectionRequest $request,
        ValidateConfigurationAction $action,
    ): JsonResponse {
        $result = $action->execute($productId, $request->toSelections());

        return response()->json([
            'data' => $result->toArray(),
        ], Response::HTTP_OK);
    }
}
