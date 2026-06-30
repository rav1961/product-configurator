<?php

declare(strict_types=1);

namespace Modules\Users\Domain\Enums;

enum Role: string
{
    case Admin = 'admin';
    case Manager = 'manager';
    case Sales = 'sales';
    case Customer = 'customer';

    public function label(): string
    {
        return match ($this) {
            self::Admin => __('users.role.admin'),
            self::Manager => __('users.role.manager'),
            self::Sales => __('users.role.sales'),
            self::Customer => __('users.role.customer'),
        };
    }

    /**
     * Higher number means more privileges.
     */
    public function rank(): int
    {
        return match ($this) {
            self::Admin => 3,
            self::Manager => 2,
            self::Sales => 1,
            self::Customer => 0,
        };
    }

    /**
     * Roles allowed to access the back-office (Filament) panel.
     *
     * @return list<string>
     */
    public static function panelRoles(): array
    {
        return [
            self::Admin->value,
            self::Manager->value,
            self::Sales->value,
        ];
    }

    /**
     * Roles this role may create/assign in the panel (strictly lower in hierarchy).
     *
     * @return list<string>
     */
    public function assignableRoles(): array
    {
        return array_values(array_map(
            static fn (self $role): string => $role->value,
            array_filter(
                self::cases(),
                fn (self $role): bool => $role->rank() < $this->rank(),
            )
        ));
    }
}
