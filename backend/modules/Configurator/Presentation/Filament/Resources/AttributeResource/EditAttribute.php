<?php

declare(strict_types=1);

namespace Modules\Configurator\Presentation\Filament\Resources\AttributeResource;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Configurator\Presentation\Filament\Resources\AttributeResource;

final class EditAttribute extends EditRecord
{
    protected static string $resource = AttributeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
