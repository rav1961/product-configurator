<?php

declare(strict_types=1);

namespace Modules\Users\Application\Actions;

use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Modules\Users\Application\DTO\ForgotPasswordData;

final class SendPasswordResetLink
{
    public function handle(ForgotPasswordData $data): void
    {
        $status = Password::sendResetLink([
            'email' => $data->email,
        ]);

        if ($status === Password::RESET_THROTTLED) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }
    }
}
