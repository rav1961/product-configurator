<?php

declare(strict_types=1);

namespace Modules\Users\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Shared\Tests\Concerns\InteractsWithSpaSession;
use Modules\Users\Domain\Models\User;
use Tests\TestCase;

final class LogoutTest extends TestCase
{
    use InteractsWithSpaSession;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withSpaSession();
    }

    public function test_authenticated_user_can_logout(): void
    {
        $this->actingAs(User::factory()->create())
            ->postJson(route('api.logout'))
            ->assertNoContent();
    }

    public function test_guest_cannot_logout(): void
    {
        $this->postJson(route('api.logout'))->assertUnauthorized();
    }
}
