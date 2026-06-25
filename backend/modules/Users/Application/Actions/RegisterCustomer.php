<?php

declare(strict_types=1);

namespace Modules\Users\Application\Actions;

use Filament\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Modules\Users\Domain\Enums\Role;
use Modules\Users\Domain\Models\User;
use Modules\Users\Presentation\Http\Requests\RegisterRequest;

final class RegisterCustomer
{
    public function handle(RegisterRequest $request): User
    {
        $user = DB::transaction(function () use ($request): User {
            $user = User::query()->create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $user->assignRole(Role::Customer->value);

            return $user;
        });

        event(new Registered($user));

        return $user;
    }
}
