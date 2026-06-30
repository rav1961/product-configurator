<?php

declare(strict_types=1);

namespace Modules\Users\Application\Actions;

use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Modules\Users\Application\DTO\RegisterData;
use Modules\Users\Domain\Contracts\UserRepository;
use Modules\Users\Domain\Enums\Role;
use Modules\Users\Domain\Models\User;

final readonly class RegisterCustomer
{
    public function __construct(
        private UserRepository $users,
    ) {}

    public function handle(RegisterData $data): User
    {
        $user = DB::transaction(function () use ($data): User {
            $user = $this->users->create([
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
