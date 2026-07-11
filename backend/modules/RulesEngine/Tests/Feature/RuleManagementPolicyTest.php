<?php

declare(strict_types=1);

namespace Modules\RulesEngine\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\RulesEngine\Domain\Models\Rule;
use Modules\RulesEngine\Presentation\Filament\Policies\RuleManagementPolicy;
use Modules\Users\Domain\Enums\Role;
use Modules\Users\Domain\Models\User;
use Modules\Users\Infrastructure\Persistence\Seeders\RoleSeeder;
use Tests\TestCase;

final class RuleManagementPolicyTest extends TestCase
{
    use RefreshDatabase;

    private RuleManagementPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);

        $this->policy = new RuleManagementPolicy;
    }

    public function test_admin_and_manager_can_manage_rules(): void
    {
        $rule = Rule::factory()->create();

        foreach ([Role::Admin, Role::Manager] as $role) {
            $user = User::factory()->create();

            $user->assignRole($role->value);
            $this->assertTrue($this->policy->viewAny($user));
            $this->assertTrue($this->policy->view($user, $rule));
            $this->assertTrue($this->policy->create($user));
            $this->assertTrue($this->policy->update($user, $rule));
            $this->assertTrue($this->policy->delete($user, $rule));
        }
    }

    public function test_sales_and_customer_cannot_manage_rules(): void
    {
        $rule = Rule::factory()->create();

        foreach ([Role::Sales, Role::Customer] as $role) {
            $user = User::factory()->create();

            $user->assignRole($role->value);

            $this->assertFalse($this->policy->viewAny($user));
            $this->assertFalse($this->policy->create($user));
            $this->assertFalse($this->policy->update($user, $rule));
            $this->assertFalse($this->policy->delete($user, $rule));
        }
    }
}
