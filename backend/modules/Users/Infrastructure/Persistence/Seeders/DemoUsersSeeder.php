<?php

declare(strict_types=1);

namespace Modules\Users\Infrastructure\Persistence\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\Users\Domain\Contracts\UserRepositoryInterface;
use Modules\Users\Domain\Enums\Role;
use RuntimeException;

final class DemoUsersSeeder extends Seeder
{
    public function run(): void
    {
        $password = config('demo-users.password');

        if (! is_string($password) || $password === '') {
            throw new RuntimeException('Demo users password must be configured.');
        }

        if (app()->isProduction() && $password === 'password') {
            throw new RuntimeException('Demo users password must be changed in production.');
        }

        /** @var array<string, array{name: mixed, email: mixed}> $usersConfig */
        $usersConfig = config('demo-users.users', []);

        /** @var UserRepositoryInterface $users */
        $users = app(UserRepositoryInterface::class);

        foreach (Role::cases() as $role) {
            $definition = $usersConfig[$role->value] ?? null;

            if (! is_array($definition)) {
                throw new RuntimeException(sprintf(
                    'Demo user definition missing for role [%s].',
                    $role->value
                ));
            }

            $email = $definition['email'] ?? null;
            $name = $definition['name'] ?? null;

            if (! is_string($email) || $email === '') {
                throw new RuntimeException('Demo user email must be configured.');
            }

            if (! is_string($name) || $name === '') {
                throw new RuntimeException('Demo user name must be configured.');
            }

            $user = $users->updateOrCreateByEmail($email, [
                'name' => $name,
                'email_verified_at' => now(),
                'password' => Hash::make($password),
            ]);

            $user->syncRoles([$role->value]);
        }
    }
}
