<?php

declare(strict_types=1);

namespace Modules\Configurator\Presentation\Filament\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Modules\Catalog\Domain\Models\Product;
use Modules\Configurator\Domain\Models\Step;
use Modules\Configurator\Presentation\Filament\Resources\StepResource;

final class StepsRelationManager extends RelationManager
{
    protected static string $relationship = 'steps';

    protected static ?string $relatedResource = StepResource::class;

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        if (! $ownerRecord instanceof Product || ! $ownerRecord->isConfigurable()) {
            return false;
        }

        return parent::canViewForRecord($ownerRecord, $pageClass);
    }

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('configurator.navigation.steps');
    }

    public function table(Table $table): Table
    {
        $owner = $this->getOwnerRecord();
        assert($owner instanceof Product);

        $productId = $owner->getKey();

        $editUrl = fn (Step $record): string => StepResource::getUrl('edit', [
            'product' => $productId,
            'record' => $record,
        ]);

        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('configurator.fields.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('position')
                    ->label(__('configurator.fields.position'))
                    ->numeric()
                    ->sortable(),
            ])
            ->defaultSort('position')
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make()
                    ->iconButton()
                    ->tooltip(__('configurator.actions.edit_step'))
                    ->url($editUrl),
                DeleteAction::make()
                    ->iconButton()
                    ->tooltip(__('configurator.actions.delete_step')),
            ], RecordActionsPosition::AfterColumns)
            ->recordActionsColumnLabel(__('configurator.fields.actions'))
            ->recordUrl($editUrl);
    }
}
