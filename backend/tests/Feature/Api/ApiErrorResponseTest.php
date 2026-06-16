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

    public function test_validation_errors_return_json_envelope(): void
    {
        $response = $this->getJson('/api/products?per_page=0');
        $response
            ->assertUnprocessable()
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'per_page',
                ],
            ]);
    }
}
