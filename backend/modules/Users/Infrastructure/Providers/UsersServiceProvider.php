<?php

declare(strict_types=1);

namespace Modules\Users\Infrastructure\Providers;

use Filament\Panel;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
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

        VerifyEmail::createUrlUsing(static fn (User $notifiable): string => URL::temporarySignedRoute(
            'api.verification.verify',
            now()->addMinutes(60),
            [
                'id' => $notifiable->public_id,
                'hash' => sha1($notifiable->getEmailForVerification()),
            ],
        ));

        ResetPassword::createUrlUsing(static fn (User $notifiable, string $token): string => rtrim(
            (string) config('app.frontend_url'),
            '/',
        ).'/reset-password?token='.urldecode($token).'&email='.urldecode($notifiable->getEmailForPasswordReset()));
    }
}
