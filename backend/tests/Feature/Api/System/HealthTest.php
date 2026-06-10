<?php

declare(strict_types=1);

namespace Tests\Feature\Api\System;

use Tests\TestCase;

final class HealthTest extends TestCase
{
    public function test_health_endpoint_returns_application_status(): void
    {
        $response = $this->getJson('/api/health');

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
