<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class AccessControlSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $adminRole = Role::findOrCreate('admin', 'web');

        $adminEmail = config('admin.email');
        $adminPassword = config('admin.password');

        if (! is_string($adminEmail) || $adminEmail === '') {
            throw new RuntimeException('Admin email must be configured.');
        }

        if (! is_string($adminPassword) || $adminPassword === '') {
            throw new RuntimeException('Admin password must be configured.');
        }

        if (app()->isProduction() && $adminPassword === 'password') {
            throw new RuntimeException('Admin password must be changed in production.');
        }

        $admin = User::query()->updateOrCreate(
            ['email' => $adminEmail],
            [
                'name' => config('admin.name', 'Admin'),
                'email_verified_at' => now(),
                'password' => Hash::make($adminPassword),
            ],
        );

        $admin->assignRole($adminRole);
    }
}
