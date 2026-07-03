<?php

declare(strict_types=1);

namespace Modules\Configurator\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Configurator\Domain\Models\Attribute;
use Modules\Configurator\Domain\Models\AttributeCollection;
use Modules\Configurator\Domain\Models\AttributeValue;
use Modules\Configurator\Domain\Models\Dependency;
use Modules\Configurator\Domain\Models\Step;
use Modules\Configurator\Presentation\Filament\Policies\ConfiguratorManagementPolicy;
use Modules\Users\Domain\Enums\Role;
use Modules\Users\Domain\Models\User;
use Modules\Users\Infrastructure\Persistence\Seeders\RoleSeeder;
use Tests\TestCase;

final class ConfiguratorManagementPolicyTest extends TestCase
{
    use RefreshDatabase;

    private ConfiguratorManagementPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);

        $this->policy = new ConfiguratorManagementPolicy;
    }

    public function test_admin_and_manager_can_manage_configurator_entities(): void
    {
        $step = Step::factory()->create();
        $attribute = Attribute::factory()->create();
        $collection = AttributeCollection::factory()->create();
        $value = AttributeValue::factory()->create();
        $dependency = Dependency::factory()->create();

        foreach ([Role::Admin, Role::Manager] as $role) {
            $user = $this->userWithRole($role);

            $this->assertTrue($this->policy->viewAny($user));
            $this->assertTrue($this->policy->view($user, $step));
            $this->assertTrue($this->policy->create($user));
            $this->assertTrue($this->policy->update($user, $attribute));
            $this->assertTrue($this->policy->delete($user, $dependency));
            $this->assertTrue($this->policy->view($user, $collection));
            $this->assertTrue($this->policy->update($user, $value));
        }
    }

    public function test_sales_and_customer_cannot_manage_configurator_entities(): void
    {
        $step = Step::factory()->create();
        $attribute = Attribute::factory()->create();
        $collection = AttributeCollection::factory()->create();
        $value = AttributeValue::factory()->create();
        $dependency = Dependency::factory()->create();

        foreach ([Role::Sales, Role::Customer] as $role) {
            $user = $this->userWithRole($role);

            $this->assertFalse($this->policy->viewAny($user));
            $this->assertFalse($this->policy->view($user, $step));
            $this->assertFalse($this->policy->create($user));
            $this->assertFalse($this->policy->update($user, $attribute));
            $this->assertFalse($this->policy->delete($user, $dependency));
            $this->assertFalse($this->policy->view($user, $collection));
            $this->assertFalse($this->policy->update($user, $value));
        }
    }

    private function userWithRole(Role $role): User
    {
        $user = User::factory()->create();

        $user->assignRole($role->value);

        return $user;
    }
}
