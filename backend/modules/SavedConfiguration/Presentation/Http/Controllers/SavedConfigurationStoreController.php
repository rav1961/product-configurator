<?php

declare(strict_types=1);

namespace Modules\SavedConfiguration\Presentation\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Modules\SavedConfiguration\Application\Actions\CreateSavedConfigurationAction;
use Modules\SavedConfiguration\Presentation\Http\Request\CreateSavedConfigurationRequest;
use Modules\Shared\Presentation\Http\Controllers\ApiController;
use Symfony\Component\HttpFoundation\Response;

final class SavedConfigurationStoreController extends ApiController
{
    public function __invoke(
        CreateSavedConfigurationRequest $request,
        CreateSavedConfigurationAction $action,
    ): JsonResponse {
        $savedConfiguration = $action->execute(
            user: $request->user(),
            productPublicId: $request->productId(),
            selection: $request->toSelection(),
        );

        return $this->responseJsonData(
            $savedConfiguration,
            Response::HTTP_CREATED,
        );
    }
}
