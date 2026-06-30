<?php

declare(strict_types=1);

namespace Modules\Users\Tests\Feature;

use Filament\Panel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Users\Domain\Enums\Role;
use Modules\Users\Domain\Models\User;
use Modules\Users\Infrastructure\Persistence\Seeders\RoleSeeder;
use Tests\TestCase;

final class PanelAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
    }

    public function test_panel_roles_can_access_admin_panel(): void
    {
        $panel = Panel::make();

        foreach ([Role::Admin, Role::Manager, Role::Sales] as $role) {
            $user = User::factory()->create();

            $user->assignRole($role->value);
            $this->assertTrue($user->canAccessPanel($panel));
        }
    }

    public function test_customer_cannot_access_admin_panel(): void
    {
        $user = User::factory()->create();

        $user->assignRole(Role::Customer->value);
        $this->assertFalse($user->canAccessPanel(Panel::make()));
    }
}
