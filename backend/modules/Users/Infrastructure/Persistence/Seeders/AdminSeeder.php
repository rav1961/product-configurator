<?php

declare(strict_types=1);

namespace Modules\Users\Infrastructure\Persistence\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\Users\Domain\Enums\Role;
use Modules\Users\Domain\Models\User;
use RuntimeException;

final class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('admin.email');
        $password = config('admin.password');

        if (! is_string($email) || $email === '') {
            throw new RuntimeException('Admin email must be configured.');
        }

        if (! is_string($password) || $password === '') {
            throw new RuntimeException('Admin password must be configured.');
        }

        if (app()->isProduction() && $password === 'password') {
            throw new RuntimeException('Admin password must be changed in production.');
        }

        $admin = User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => config('admin.name', 'Admin'),
                'email_verified_at' => now(),
                'password' => Hash::make($password),
            ],
        );

        $admin->assignRole(Role::Admin->value);
    }
}
