<?php

declare(strict_types=1);

namespace Modules\Configurator\Presentation\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Modules\Configurator\Application\Actions\EvaluateConfigurationAction;
use Modules\Configurator\Application\DTO\ConfigurationAttributeStateData;
use Modules\Configurator\Presentation\Http\Requests\ConfigurationSelectionRequest;
use Modules\Shared\Presentation\Http\Controllers\ApiController;
use Symfony\Component\HttpFoundation\Response;

final class ConfiguratorEvaluateController extends ApiController
{
    public function __invoke(
        string $productId,
        ConfigurationSelectionRequest $request,
        EvaluateConfigurationAction $action,
    ): JsonResponse {
        $evaluation = $action->execute($productId, $request->toSelections());

        return response()->json([
            'data' => [
                'productId' => $evaluation->productId,
                'attributes' => collect($evaluation->attributes)
                    ->mapWithKeys(static fn (ConfigurationAttributeStateData $state, string $key): array => [
                        $key => $state->toArray(),
                    ])
                    ->all(),
            ],
        ], Response::HTTP_OK);
    }
}
