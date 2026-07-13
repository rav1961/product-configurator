<?php

declare(strict_types=1);

namespace Modules\SavedConfiguration\Presentation\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\SavedConfiguration\Application\Actions\GetSavedConfigurationAction;
use Modules\Shared\Presentation\Http\Controllers\ApiController;

final class SavedConfigurationShowController extends ApiController
{
    public function __invoke(
        string $savedConfigurationId,
        Request $request,
        GetSavedConfigurationAction $action,
    ): JsonResponse {
        $savedConfiguration = $action->execute(
            user: $request->user(),
            savedConfigurationPublicId: $savedConfigurationId,
        );

        return $this->responseJsonData($savedConfiguration);
    }
}
