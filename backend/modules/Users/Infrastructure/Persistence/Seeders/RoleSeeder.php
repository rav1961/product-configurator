<?php

declare(strict_types=1);

namespace Modules\Users\Infrastructure\Persistence\Seeders;

use Illuminate\Database\Seeder;
use Modules\Users\Domain\Enums\Role as RoleEnum;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

final class RoleSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (RoleEnum::cases() as $role) {
            Role::findOrCreate($role->value, 'web');
        }
    }
}
