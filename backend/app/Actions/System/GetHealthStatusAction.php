<?php

declare(strict_types=1);

namespace App\Actions\System;

use App\Data\System\HealthStatusData;

final class GetHealthStatusAction
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
