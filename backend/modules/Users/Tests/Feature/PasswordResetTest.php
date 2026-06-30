<?php

declare(strict_types=1);

namespace Modules\Users\Tests\Feature;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Modules\Shared\Tests\Concerns\InteractsWithSpaSession;
use Modules\Users\Domain\Models\User;
use Tests\TestCase;

final class PasswordResetTest extends TestCase
{
    use InteractsWithSpaSession;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withSpaSession();
    }

    public function test_forgot_password_sends_notification_for_existing_user(): void
    {
        Notification::fake();

        $user = User::factory()->create(['email' => 'user@example.test']);

        $this->postJson(route('api.password.forgot'), [
            'email' => 'user@example.test',
        ])->assertNoContent();

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_forgot_password_returns_no_content_for_unknown_email(): void
    {
        Notification::fake();

        $this->postJson(route('api.password.forgot'), [
            'email' => 'unknown@example.test',
        ])->assertNoContent();

        Notification::assertNothingSent();
    }

    public function test_forgot_password_validates_email(): void
    {
        $this->postJson(route('api.password.forgot'), [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    public function test_user_can_reset_password_with_valid_token(): void
    {
        Event::fake([PasswordReset::class]);

        $user = User::factory()->create(['email' => 'user@example.test']);
        $token = Password::createToken($user);

        $this->postJson(route('api.password.reset'), [
            'token' => $token,
            'email' => 'user@example.test',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ])->assertNoContent();

        $this->assertTrue(Hash::check('NewPassword123!', $user->refresh()->password));

        Event::assertDispatched(PasswordReset::class);
    }

    public function test_reset_password_rejects_invalid_token(): void
    {
        User::factory()->create(['email' => 'user@example.test']);

        $this->postJson(route('api.password.reset'), [
            'token' => 'invalid-token',
            'email' => 'user@example.test',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    public function test_reset_password_validates_payload(): void
    {
        $this->postJson(route('api.password.reset'), [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['token', 'email', 'password']);
    }

    public function test_user_can_login_with_new_password_after_reset(): void
    {
        $user = User::factory()->create(['email' => 'user@example.test']);
        $token = Password::createToken($user);

        $this->postJson(route('api.password.reset'), [
            'token' => $token,
            'email' => 'user@example.test',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ])->assertNoContent();

        $this->postJson(route('api.login'), [
            'email' => 'user@example.test',
            'password' => 'NewPassword123!',
        ])->assertOk();
    }
}
