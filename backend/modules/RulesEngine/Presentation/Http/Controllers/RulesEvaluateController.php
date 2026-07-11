<?php

declare(strict_types=1);

namespace Modules\RulesEngine\Presentation\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Modules\Configurator\Presentation\Http\Requests\ConfigurationSelectionRequest;
use Modules\RulesEngine\Application\Actions\EvaluateRulesAction;
use Modules\Shared\Presentation\Http\Controllers\ApiController;
use Symfony\Component\HttpFoundation\Response;

final class RulesEvaluateController extends ApiController
{
    public function __invoke(
        string $productId,
        ConfigurationSelectionRequest $request,
        EvaluateRulesAction $action,
    ): JsonResponse {
        $evaluation = $action->execute($productId, $request->toSelections());

        return response()->json([
            'data' => $evaluation->toResponseArray(),
        ], Response::HTTP_OK);
    }
}
