<?php

declare(strict_types=1);

namespace Modules\RulesEngine\Presentation\Filament\Resources\RuleResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\RulesEngine\Presentation\Filament\Resources\RuleResource;

final class EditRule extends EditRecord
{
    protected static string $resource = RuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
