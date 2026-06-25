<?php

declare(strict_types=1);

namespace Modules\Users\Presentation\Http\Controllers;

use Modules\Shared\Presentation\Http\Controllers\ApiController;
use Modules\Users\Application\DTO\UserData;
use Modules\Users\Presentation\Http\Requests\LoginRequest;

final class LoginController extends ApiController
{
    public function __invoke(LoginRequest $request): UserData
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = $request->user();

        return UserData::fromModel($user->loadMissing('roles'));
    }
}
