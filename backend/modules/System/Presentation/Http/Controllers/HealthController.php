<?php

declare(strict_types=1);

namespace Modules\System\Presentation\Http\Controllers;

use Modules\Shared\Presentation\Http\Controllers\ApiController;
use Modules\System\Application\Actions\GetHealthStatusAction;
use Modules\System\Application\DTO\HealthStatusData;

final class HealthController extends ApiController
{
    public function __invoke(
        GetHealthStatusAction $action,
    ): HealthStatusData {
        return $action->execute();
    }
}
