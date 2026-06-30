<?php

declare(strict_types=1);

namespace Modules\Users\Tests\Unit;

use Modules\Users\Domain\Enums\Role;
use Tests\TestCase;

final class RoleTest extends TestCase
{
    public function test_rank_orders_roles_by_privilege(): void
    {
        $this->assertGreaterThan(Role::Manager->rank(), Role::Admin->rank());
        $this->assertGreaterThan(Role::Sales->rank(), Role::Manager->rank());
        $this->assertGreaterThan(Role::Customer->rank(), Role::Sales->rank());
    }

    public function test_panel_roles_exclude_customer(): void
    {
        $this->assertSame(['admin', 'manager', 'sales'], Role::panelRoles());
        $this->assertNotContains(Role::Customer->value, Role::panelRoles());
    }

    public function test_assignable_roles_are_strictly_lowe_in_hierarchy(): void
    {
        $this->assertSame(['manager', 'sales', 'customer'], Role::Admin->assignableRoles());
        $this->assertSame(['sales', 'customer'], Role::Manager->assignableRoles());
        $this->assertSame(['customer'], Role::Sales->assignableRoles());
        $this->assertSame([], Role::Customer->assignableRoles());
    }

    public function test_label_resolves_polish_translation(): void
    {
        $this->assertSame('Administrator', Role::Admin->label());
        $this->assertSame('Klient', Role::Customer->label());
    }
}
