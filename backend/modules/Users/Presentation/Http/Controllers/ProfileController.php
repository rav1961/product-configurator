<?php

declare(strict_types=1);

namespace Modules\Users\Presentation\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Shared\Presentation\Http\Controllers\ApiController;
use Modules\Users\Application\DTO\UserData;

final class ProfileController extends ApiController
{
    public function __invoke(Request $request): UserData
    {
        $user = $request->user();

        return UserData::fromModel($user->loadMissing('roles'));
    }
}
