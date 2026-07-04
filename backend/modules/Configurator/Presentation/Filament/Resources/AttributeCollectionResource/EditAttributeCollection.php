<?php

declare(strict_types=1);

namespace Modules\Configurator\Presentation\Filament\Resources\AttributeCollectionResource;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Configurator\Presentation\Filament\Resources\AttributeCollectionResource;

final class EditAttributeCollection extends EditRecord
{
    protected static string $resource = AttributeCollectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
