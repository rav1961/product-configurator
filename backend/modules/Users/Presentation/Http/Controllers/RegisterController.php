<?php

declare(strict_types=1);

namespace Modules\Users\Presentation\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Modules\Shared\Presentation\Http\Controllers\ApiController;
use Modules\Users\Application\Actions\RegisterCustomer;
use Modules\Users\Application\DTO\RegisterData;
use Modules\Users\Application\DTO\UserData;
use Modules\Users\Presentation\Http\Requests\RegisterRequest;
use Symfony\Component\HttpFoundation\Response;

final class RegisterController extends ApiController
{
    public function __invoke(
        RegisterRequest $request,
        RegisterCustomer $action,
    ): JsonResponse {
        $validated = $request->validated();

        $user = $action->handle(RegisterData::from($validated));

        return UserData::fromModel($user->loadMissing('roles'))
            ->toResponse($request)
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
