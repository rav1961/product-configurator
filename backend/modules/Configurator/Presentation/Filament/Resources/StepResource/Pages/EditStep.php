<?php

declare(strict_types=1);

namespace Modules\Configurator\Presentation\Filament\Resources\StepResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Configurator\Presentation\Filament\Resources\StepResource;

final class EditStep extends EditRecord
{
    protected static string $resource = StepResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
