<?php

declare(strict_types=1);

namespace Modules\Users\Presentation\Filament\Resources\UserResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Users\Presentation\Filament\Resources\UserResource;

final class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}
