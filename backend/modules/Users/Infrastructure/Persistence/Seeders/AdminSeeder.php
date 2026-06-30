<?php

declare(strict_types=1);

namespace Modules\Users\Infrastructure\Persistence\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\Users\Domain\Contracts\UserRepositoryInterface;
use Modules\Users\Domain\Enums\Role;
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

        /** @var UserRepositoryInterface $users */
        $users = app(UserRepositoryInterface::class);

        $admin = $users->updateOrCreateByEmail($email, [
            'name' => config('admin.name', 'Admin'),
            'email_verified_at' => now(),
            'password' => Hash::make($password),
        ]);

        $admin->assignRole(Role::Admin->value);
    }
}
