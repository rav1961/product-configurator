<?php

declare(strict_types=1);

namespace Modules\Users\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Shared\Tests\Concerns\InteractsWithSpaSession;
use Modules\Users\Domain\Enums\Role;
use Modules\Users\Domain\Models\User;
use Modules\Users\Infrastructure\Persistence\Seeders\RoleSeeder;
use Tests\TestCase;

final class ProfileTest extends TestCase
{
    use InteractsWithSpaSession;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
        $this->withSpaSession();
    }

    public function test_authenticated_user_can_fetch_profile(): void
    {
        $user = User::factory()->create(['email' => 'me@example.test']);
        $user->assignRole(Role::Customer->value);

        $this->actingAs($user)
            ->getJson(route('api.profile'))
            ->assertOk()
            ->assertJsonPath('data.email', 'me@example.test')
            ->assertJsonPath('data.roles', [Role::Customer->value]);
    }

    public function test_guest_cannot_fetch_profile(): void
    {
        $this->getJson(route('api.profile'))->assertUnauthorized();
    }
}
