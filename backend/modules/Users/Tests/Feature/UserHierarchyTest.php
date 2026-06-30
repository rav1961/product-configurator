<?php

declare(strict_types=1);

namespace Modules\Users\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Users\Domain\Enums\Role;
use Modules\Users\Domain\Models\User;
use Modules\Users\Infrastructure\Persistence\Seeders\RoleSeeder;
use Tests\TestCase;

final class UserHierarchyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
    }

    public function test_highest_role_returns_top_ranked_role(): void
    {
        $user = User::factory()->create();
        $user->assignRole([Role::Sales->value, Role::Manager->value]);

        $this->assertSame(Role::Manager, $user->highestRole());
        $this->assertSame(Role::Manager->rank(), $user->rank());
    }

    public function test_user_without_roles_has_no_highest_role(): void
    {
        $user = User::factory()->create();

        $this->assertNull($user->highestRole());
        $this->assertSame(-1, $user->rank());
    }
}
