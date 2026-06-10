<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Tests\TestCase;

final class ApiErrorResponseTest extends TestCase
{
    public function test_protected_api_route_returns_json_unauthenticated_response(): void
    {
        $response = $this->getJson('/api/user');

        $response
            ->assertUnauthorized()
            ->assertExactJson([
                'message' => 'Unauthenticated',
            ]);
    }
}
