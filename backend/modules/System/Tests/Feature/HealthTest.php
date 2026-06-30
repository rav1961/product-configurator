<?php

declare(strict_types=1);

namespace Modules\System\Tests\Feature;

use Tests\TestCase;

final class HealthTest extends TestCase
{
    public function test_health_endpoint_returns_application_status(): void
    {
        $response = $this->getJson(route('api.health'));

        $response
            ->assertOk()
            ->assertJsonPath('data.status', 'ok')
            ->assertJsonPath('data.app', config('app.name'))
            ->assertJsonStructure([
                'data' => [
                    'status',
                    'app',
                    'environment',
                    'timestamp',
                ],
            ]);
    }
}
