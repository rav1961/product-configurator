<?php

declare(strict_types=1);

namespace Modules\Pricing\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Pricing\Domain\Models\ProductPrice;
use Modules\Pricing\Presentation\Filament\Policies\PricingManagementPolicy;
use Modules\Users\Domain\Enums\Role;
use Modules\Users\Domain\Models\User;
use Modules\Users\Infrastructure\Persistence\Seeders\RoleSeeder;
use Tests\TestCase;

final class PricingPolicyTest extends TestCase
{
    use RefreshDatabase;

    private PricingManagementPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);

        $this->policy = new PricingManagementPolicy;
    }

    public function test_admin_and_manager_can_manage_product_prices(): void
    {
        $productPrice = ProductPrice::factory()->create();

        foreach ([Role::Admin, Role::Manager] as $role) {
            $user = $this->userWithRole($role);

            $this->assertTrue($this->policy->viewAny($user));
            $this->assertTrue($this->policy->view($user, $productPrice));
            $this->assertTrue($this->policy->create($user));
            $this->assertTrue($this->policy->update($user, $productPrice));
            $this->assertTrue($this->policy->delete($user, $productPrice));
        }
    }

    public function test_sales_cannot_manage_product_prices(): void
    {
        $productPrice = ProductPrice::factory()->create();
        $user = $this->userWithRole(Role::Sales);

        $this->assertFalse($this->policy->viewAny($user));
        $this->assertFalse($this->policy->view($user, $productPrice));
        $this->assertFalse($this->policy->create($user));
        $this->assertFalse($this->policy->update($user, $productPrice));
        $this->assertFalse($this->policy->delete($user, $productPrice));
    }

    private function userWithRole(Role $role): User
    {
        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }
}
