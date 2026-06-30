<?php

declare(strict_types=1);

namespace Modules\Users\Domain\Contracts;

use Modules\Users\Domain\Models\User;

interface UserRepository
{
    public function findByPublicId(string $publicId): User;

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): User;
}
