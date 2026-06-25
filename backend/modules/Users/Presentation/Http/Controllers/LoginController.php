<?php

declare(strict_types=1);

namespace Modules\Users\Presentation\Http\Controllers;

use Modules\Shared\Presentation\Http\Controllers\ApiController;
use Modules\Users\Application\DTO\UserData;
use Modules\Users\Domain\Models\User;
use Modules\Users\Presentation\Http\Requests\LoginRequest;
use Symfony\Component\HttpFoundation\Response;

final class LoginController extends ApiController
{
    public function __invoke(LoginRequest $request): Response
    {
        $request->authenticate();

        $request->session()->regenerate();

        /** @var User $user */
        $user = $request->user();

        return UserData::fromModel($user->loadMissing('roles'))
            ->toResponse($request)
            ->setStatusCode(Response::HTTP_OK);
    }
}
