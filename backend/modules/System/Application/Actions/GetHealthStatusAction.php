<?php

declare(strict_types=1);

namespace Modules\System\Application\Actions;

use Modules\System\Application\DTO\HealthStatusData;

final readonly class GetHealthStatusAction
{
    public function execute(): HealthStatusData
    {
        return new HealthStatusData(
            status: 'ok',
            app: (string) config('app.name'),
            environment: app()->environment(),
            timestamp: now()->toISOString(),
        );
    }
}
