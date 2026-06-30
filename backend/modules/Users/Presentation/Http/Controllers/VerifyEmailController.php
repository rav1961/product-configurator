<?php

declare(strict_types=1);

namespace Modules\Users\Presentation\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Modules\Shared\Presentation\Http\Controllers\ApiController;
use Modules\Users\Application\Actions\VerifyUserEmail;
use Modules\Users\Domain\Exceptions\InvalidEmailVerificationHash;

final class VerifyEmailController extends ApiController
{
    public function __invoke(
        string $id,
        string $hash,
        VerifyUserEmail $action,
    ): RedirectResponse {
        try {
            return redirect()->away($action->handle($id, $hash));
        } catch (InvalidEmailVerificationHash) {
            abort(403);
        }
    }
}
