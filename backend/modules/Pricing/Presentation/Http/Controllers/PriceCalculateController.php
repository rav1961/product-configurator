<?php

declare(strict_types=1);

namespace Modules\Pricing\Presentation\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Modules\Configurator\Presentation\Http\Requests\ConfigurationSelectionRequest;
use Modules\Pricing\Application\Actions\CalculatePriceAction;
use Modules\Shared\Presentation\Http\Controllers\ApiController;
use Symfony\Component\HttpFoundation\Response;

final class PriceCalculateController extends ApiController
{
    public function __invoke(
        string $productId,
        ConfigurationSelectionRequest $request,
        CalculatePriceAction $action,
    ): JsonResponse {
        $calculation = $action->execute($productId, $request->toSelections());

        return response()->json([
            'data' => $calculation->toResponseArray(),
        ], Response::HTTP_OK);
    }
}
