<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Users\Domain\Models\User;
use Tests\TestCase;

final class ApiErrorResponseTest extends TestCase
{
    use RefreshDatabase;

    public function test_protected_api_route_returns_json_unauthenticated_response(): void
    {
        $response = $this->getJson(route('api.profile'));

        $response
            ->assertUnauthorized()
            ->assertExactJson([
                'message' => 'Unauthenticated',
            ]);
    }

    public function test_validation_errors_return_json_envelope(): void
    {
        $response = $this->actingAs(User::factory()->create())
            ->getJson('/api/products?per_page=0');
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
