<?php

declare(strict_types=1);

namespace Modules\Catalog\Presentation\Filament\Resources\CategoryResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Catalog\Presentation\Filament\Resources\CategoryResource;

final class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;
}
