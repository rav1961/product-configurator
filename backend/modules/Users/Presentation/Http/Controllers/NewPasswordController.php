<?php

declare(strict_types=1);

namespace Modules\Users\Presentation\Http\Controllers;

use Illuminate\Http\Response;
use Modules\Shared\Presentation\Http\Controllers\ApiController;
use Modules\Users\Application\Actions\ResetUserPassword;
use Modules\Users\Application\DTO\ResetPasswordData;
use Modules\Users\Presentation\Http\Requests\ResetPasswordRequest;

final class NewPasswordController extends ApiController
{
    public function __invoke(
        ResetPasswordRequest $request,
        ResetUserPassword $action,
    ): Response {
        $validated = $request->validated();

        $action->handle(new ResetPasswordData(
            token: $validated['token'],
            email: $validated['email'],
            password: $validated['password'],
        ));

        return response()->noContent();
    }
}
