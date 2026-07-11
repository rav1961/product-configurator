<?php

declare(strict_types=1);

namespace Modules\RulesEngine\Presentation\Filament\Resources\RuleGroupResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\RulesEngine\Presentation\Filament\Resources\RuleGroupResource;

final class EditRuleGroup extends EditRecord
{
    protected static string $resource = RuleGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
