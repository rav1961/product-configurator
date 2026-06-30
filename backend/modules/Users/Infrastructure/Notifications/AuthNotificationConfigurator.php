<?php

declare(strict_types=1);

namespace Modules\Users\Infrastructure\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;
use Modules\Users\Domain\Models\User;

final class AuthNotificationConfigurator
{
    public function configure(): void
    {
        VerifyEmail::createUrlUsing($this->verificationUrl(...));
        VerifyEmail::toMailUsing($this->verificationMail(...));
        ResetPassword::createUrlUsing($this->resetPasswordUrl(...));
        ResetPassword::toMailUsing($this->resetPasswordMail(...));
    }

    private function verificationUrl(User $notifiable): string
    {
        return URL::temporarySignedRoute(
            'api.verification.verify',
            now()->addMinutes(60),
            [
                'id' => $notifiable->public_id,
                'hash' => sha1($notifiable->getEmailForVerification()),
            ],
        );
    }

    private function verificationMail(object $notifiable, string $url): MailMessage
    {
        return (new MailMessage)
            ->subject(__('users.mail.verify.subject'))
            ->line(__('users.mail.verify.line1'))
            ->action(__('users.mail.verify.action'), $url)
            ->line(__('users.mail.verify.line2', [
                'count' => config('auth.verification.expire', 60),
            ]));
    }

    private function resetPasswordUrl(User $notifiable, string $token): string
    {
        return rtrim((string) config('app.frontend_url'), '/')
            .'/reset-password?token='.urlencode($token)
            .'&email='.urlencode($notifiable->getEmailForPasswordReset());
    }

    private function resetPasswordMail(object $notifiable, string $token): MailMessage
    {
        /** @var User $notifiable */
        $url = $this->resetPasswordUrl($notifiable, $token);

        return (new MailMessage)
            ->subject(__('users.mail.reset.subject'))
            ->line(__('users.mail.reset.line1'))
            ->action(__('users.mail.reset.action'), $url)
            ->line(__('users.mail.reset.line2', [
                'count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire'),
            ]))
            ->line(__('users.mail.reset.line3'));
    }
}
