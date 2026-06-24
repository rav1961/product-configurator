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
            self::Admin => 'Administrator',
            self::Manager => 'Manager',
            self::Sales => 'Sales',
            self::Customer => 'Customer',
        };
    }

    /**
     * @return list<string>
     */
    public static function panelRoles(): array
    {
        return [
            self::Admin,
            self::Manager,
            self::Sales,
        ];
    }

    /**
     * @return list<string>
     */
    public function values(): array
    {
        return array_map(static fn (self $role): string => $role->value, self::cases());
    }
}
