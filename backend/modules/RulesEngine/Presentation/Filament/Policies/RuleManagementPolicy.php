<?php

declare(strict_types=1);

namespace Modules\RulesEngine\Presentation\Filament\Policies;

use Illuminate\Database\Eloquent\Model;
use Modules\Users\Domain\Enums\Role;
use Modules\Users\Domain\Models\User;

final class RuleManagementPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(Role::catalogManagementRoles());
    }

    public function view(User $user, Model $model): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, Model $model): bool
    {
        return $this->viewAny($user);
    }

    public function delete(User $user, Model $model): bool
    {
        return $this->viewAny($user);
    }
}
