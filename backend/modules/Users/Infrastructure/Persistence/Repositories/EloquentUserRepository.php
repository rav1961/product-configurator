<?php

declare(strict_types=1);

namespace Modules\Users\Infrastructure\Persistence\Repositories;

use Modules\Users\Domain\Contracts\UserRepositoryInterface;
use Modules\Users\Domain\Models\User;

final class EloquentUserRepository implements UserRepositoryInterface
{
    public function findByPublicId(string $publicId): User
    {
        return User::query()
            ->where('public_id', $publicId)
            ->firstOrFail();
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): User
    {
        return User::query()->create($attributes);
    }

    /**
     * @param  array<string, mixed>  $values
     */
    public function updateOrCreateByEmail(string $email, array $values): User
    {
        return User::query()->updateOrCreate(
            ['email' => $email],
            $values,
        );
    }
}
