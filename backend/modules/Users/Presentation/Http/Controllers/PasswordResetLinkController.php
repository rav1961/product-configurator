<?php

declare(strict_types=1);

namespace Modules\Users\Presentation\Http\Controllers;

use Illuminate\Http\Response;
use Modules\Shared\Presentation\Http\Controllers\ApiController;
use Modules\Users\Application\Actions\SendPasswordResetLink;
use Modules\Users\Application\DTO\ForgotPasswordData;
use Modules\Users\Presentation\Http\Requests\ForgotPasswordRequest;

final class PasswordResetLinkController extends ApiController
{
    public function __invoke(
        ForgotPasswordRequest $request,
        SendPasswordResetLink $action,
    ): Response {
        $action->handle(ForgotPasswordData::from($request->validated()));

        return response()->noContent();
    }
}
