<?php

declare(strict_types=1);

namespace Modules\Users\Application\Actions;

use Filament\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Modules\Users\Application\DTO\RegisterData;
use Modules\Users\Domain\Enums\Role;
use Modules\Users\Domain\Models\User;

final class RegisterCustomer
{
    public function handle(RegisterData $data): User
    {
        $user = DB::transaction(function () use ($data): User {
            $user = User::query()->create([
                'name' => $data->name,
                'email' => $data->email,
                'password' => Hash::make($data->password),
            ]);

            $user->assignRole(Role::Customer->value);

            return $user;
        });

        event(new Registered($user));

        return $user;
    }
}
