<?php

declare(strict_types=1);

namespace Modules\Users\Infrastructure\Providers;

use Filament\Panel;
use Illuminate\Support\Facades\Gate;
use Modules\Shared\Infrastructure\Providers\ModuleServiceProvider;
use Modules\Users\Domain\Models\User;
use Modules\Users\Presentation\Filament\Policies\UserPolicy;
use Modules\Users\Presentation\Filament\Resources\UserResource;

final class UsersServiceProvider extends ModuleServiceProvider
{
    protected function modulePath(): string
    {
        return dirname(__DIR__, 2);
    }

    public function register(): void
    {
        Panel::configureUsing(static function (Panel $panel): void {
            if ($panel->getId() !== 'admin') {
                return;
            }

            $panel->resources([
                UserResource::class,
            ]);
        });
    }

    public function boot(): void
    {
        parent::boot();

        Gate::policy(User::class, UserPolicy::class);
    }
}
