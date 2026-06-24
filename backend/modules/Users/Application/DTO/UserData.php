<?php

declare(strict_types=1);

namespace Modules\Users\Application\DTO;

use Modules\Users\Domain\Models\User;
use Spatie\LaravelData\Data;

final class UserData extends Data
{
    /**
     * @param  list<string>  $roles
     */
    public function __construct(
        public string $id,
        public string $name,
        public string $email,
        public string $emailVerified,
        public array $roles,
    ) {}

    public static function fromModel(User $user): self
    {
        return new self(
            id: $user->public_id,
            name: $user->name,
            email: $user->email,
            emailVerified: $user->hasVerifiedEmail(),
            roles: array_values($user->getRoleNames()->all()),
        );
    }
}
