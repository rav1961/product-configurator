<?php

declare(strict_types=1);

namespace Modules\Users\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Users\Domain\Enums\Role;
use Modules\Users\Domain\Models\User;
use Modules\Users\Infrastructure\Persistence\Seeders\RoleSeeder;
use Modules\Users\Presentation\Filament\Policies\UserPolicy;
use Tests\TestCase;

final class UserPolicyTest extends TestCase
{
    use RefreshDatabase;

    private UserPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
        $this->policy = new UserPolicy;
    }

    public function test_higher_rank_can_manage_lower_rank(): void
    {
        $admin = $this->userWithRole(Role::Admin);
        $manager = $this->userWithRole(Role::Manager);

        $this->assertTrue($this->policy->view($admin, $manager));
        $this->assertTrue($this->policy->update($admin, $manager));
        $this->assertTrue($this->policy->delete($admin, $manager));
    }

    public function test_cannot_manage_equal_or_higher_rank(): void
    {
        $manager = $this->userWithRole(Role::Manager);
        $admin = $this->userWithRole(Role::Admin);

        $this->assertFalse($this->policy->view($manager, $admin));
        $this->assertFalse($this->policy->update($manager, $admin));
        $this->assertFalse($this->policy->delete($manager, $admin));
    }

    public function test_user_can_view_and_update_self_but_not_delete_self(): void
    {
        $manager = $this->userWithRole(Role::Manager);

        $this->assertTrue($this->policy->view($manager, $manager));
        $this->assertTrue($this->policy->update($manager, $manager));
        $this->assertFalse($this->policy->delete($manager, $manager));
    }

    public function test_view_any_and_create_require_panel_role(): void
    {
        $sales = $this->userWithRole(Role::Sales);
        $customer = $this->userWithRole(Role::Customer);

        $this->assertTrue($this->policy->viewAny($sales));
        $this->assertTrue($this->policy->create($sales));
        $this->assertFalse($this->policy->viewAny($customer));
        $this->assertFalse($this->policy->create($customer));
    }

    private function userWithRole(Role $role): User
    {
        $user = User::factory()->create();
        $user->assignRole($role->value);

        return $user;
    }
}
