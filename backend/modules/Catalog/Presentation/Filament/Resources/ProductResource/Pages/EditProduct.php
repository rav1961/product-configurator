<?php

declare(strict_types=1);

namespace Modules\Catalog\Presentation\Filament\Resources\ProductResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Catalog\Domain\Models\Product;
use Modules\Catalog\Presentation\Filament\Resources\ProductResource;

final class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    public function getRelationManagers(): array
    {
        $record = $this->getRecord();

        if (! $record instanceof Product || ! $record->isConfigurable()) {
            return [];
        }

        return parent::getRelationManagers();
    }
}
