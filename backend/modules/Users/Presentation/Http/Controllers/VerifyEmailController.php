<?php

declare(strict_types=1);

namespace Modules\Users\Presentation\Http\Controllers;

use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Shared\Presentation\Http\Controllers\ApiController;
use Modules\Users\Domain\Models\User;

final class VerifyEmailController extends ApiController
{
    public function __invoke(
        Request $request,
        string $id,
        string $hash,
    ): RedirectResponse {
        $user = User::query()->where('public_id', $id)->firstOrFail();

        if (! hash_equals($hash, sha1($user->getEmailForVerification()))) {
            abort(403);
        }

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();

            event(new Verified($user));
        }

        return redirect()->away(
            rtrim((string) config('app.frontend_url'), '/').'/email/verified',
        );
    }
}
