<?php

declare(strict_types=1);

namespace Modules\Users\Tests\Feature;

use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Modules\Shared\Tests\Concerns\InteractsWithSpaSession;
use Modules\Users\Domain\Models\User;
use Modules\Users\Infrastructure\Persistence\Seeders\RoleSeeder;
use Tests\TestCase;

final class EmailVerificationTest extends TestCase
{
    use InteractsWithSpaSession;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
        $this->withSpaSession();
    }

    public function test_registration_sends_verification_notification(): void
    {
        Notification::fake();

        $this->postJson(route('api.register'), [
            'name' => 'Jan Kowalski',
            'email' => 'jan@example.test',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ])->assertCreated();

        $user = User::query()->where('email', 'jan@example.test')->firstOrFail();

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_user_can_verify_email_via_signed_url(): void
    {
        Event::fake([Verified::class]);

        $user = User::factory()->unverified()->create();

        $this->get($this->verificationUrl($user))->assertRedirect();
        $this->assertTrue($user->refresh()->hasVerifiedEmail());

        Event::assertDispatched(Verified::class);
    }

    public function test_verification_fails_with_invalid_hash(): void
    {
        $user = User::factory()->unverified()->create();

        $url = URL::temporarySignedRoute(
            'api.verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->public_id,
                'hash' => sha1('wrong@example.test'),
            ],
        );

        $this->get($url)->assertForbidden();

        $this->assertFalse($user->refresh()->hasVerifiedEmail());
    }

    public function test_authenticated_user_can_resend_verification(): void
    {
        Notification::fake();

        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->postJson(route('api.verification.send'))
            ->assertNoContent();

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_resend_is_noop_for_verified_user(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson(route('api.verification.send'))
            ->assertNoContent();

        Notification::assertNothingSent();
    }

    public function test_unverified_user_cannot_access_catalog(): void
    {
        $this->actingAs(User::factory()->unverified()->create())
            ->getJson(route('api.products.list'))
            ->assertForbidden();
    }

    private function verificationUrl(User $user): string
    {
        return URL::temporarySignedRoute(
            'api.verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->public_id,
                'hash' => sha1($user->getEmailForVerification()),
            ],
        );
    }
}
