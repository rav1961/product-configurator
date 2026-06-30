<?php

declare(strict_types=1);

namespace Modules\Users\Tests\Feature;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Modules\Shared\Tests\Concerns\InteractsWithSpaSession;
use Modules\Users\Domain\Models\User;
use Tests\TestCase;

final class LoginTest extends TestCase
{
    use InteractsWithSpaSession;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withSpaSession();
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        User::factory()->create(['email' => 'user@example.test']);

        $this->postJson(route('api.login'), [
            'email' => 'user@example.test',
            'password' => 'password',
        ])
            ->assertOk()
            ->assertJsonPath('data.email', 'user@example.test');

        $this->assertAuthenticated();
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        User::factory()->create(['email' => 'user@example.test']);

        $this->postJson(route('api.login'), [
            'email' => 'user@example.test',
            'password' => 'wrong-password',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email']);

        $this->assertGuest();
    }

    public function test_login_is_rate_limited_after_too_many_attempts(): void
    {
        Event::fake([Lockout::class]);
        User::factory()->create(['email' => 'user@example.test']);

        foreach (range(1, 5) as $ignored) {
            $this->postJson('/api/login', [
                'email' => 'user@example.test',
                'password' => 'wrong-password',
            ])->assertStatus(422);
        }

        $this->postJson(route('api.login'), [
            'email' => 'user@example.test',
            'password' => 'wrong-password',
        ])->assertStatus(422);

        Event::assertDispatched(Lockout::class);
    }
}
