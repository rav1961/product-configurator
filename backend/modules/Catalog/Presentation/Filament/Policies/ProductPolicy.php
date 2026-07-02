<?php

declare(strict_types=1);

namespace Modules\Catalog\Presentation\Filament\Policies;

use Modules\Catalog\Domain\Models\Product;
use Modules\Users\Domain\Enums\Role;
use Modules\Users\Domain\Models\User;

final class ProductPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(Role::catalogManagementRoles());
    }

    public function view(User $user, Product $model): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, Product $model): bool
    {
        return $this->viewAny($user);
    }

    public function delete(User $user, Product $model): bool
    {
        return $this->viewAny($user);
    }
}
