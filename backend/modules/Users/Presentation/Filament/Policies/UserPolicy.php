<?php

declare(strict_types=1);

namespace Modules\Users\Presentation\Filament\Policies;

use Modules\Users\Domain\Enums\Role;
use Modules\Users\Domain\Models\User;

final class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(Role::panelRoles());
    }

    public function view(User $user, User $model): bool
    {
        return $user->is($model) || $this->outranks($user, $model);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(Role::panelRoles());
    }

    public function update(User $user, User $model): bool
    {
        return $user->is($model) || $this->outranks($user, $model);
    }

    public function delete(User $user, User $model): bool
    {
        return ! $user->is($model) && $this->outranks($user, $model);
    }

    private function outranks(User $actor, User $target): bool
    {
        return $this->highestRank($actor) > $this->highestRank($target);
    }

    private function highestRank(User $user): int
    {
        return $user->getRoleNames()
            ->map(static fn (string $name): ?Role => Role::tryFrom($name))
            ->filter()
            ->map(static fn (Role $role): int => $role->rank())
            ->max() ?? -1;
    }
}
