<?php

declare(strict_types=1);

namespace Modules\Users\Presentation\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Shared\Presentation\Http\Controllers\ApiController;
use Modules\Users\Domain\Models\User;

final class SendVerificationEmailController extends ApiController
{
    public function __invoke(Request $request): Response
    {
        /** @var User $user */
        $user = $request->user();

        if (! $user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();
        }

        return response()->noContent();
    }
}
