<?php

declare(strict_types=1);

namespace Modules\Configurator\Presentation\Filament\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Modules\Configurator\Domain\Models\AttributeCollection;

final class CollectionValuesRelationManager extends RelationManager
{
    protected static string $relationship = 'values';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('configurator.navigation.values');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('label')
                ->label(__('configurator.fields.label'))
                ->required()
                ->maxLength(255)
                ->live(onBlur: true)
                ->afterStateUpdated(static function (string $operation, ?string $state, Set $set): void {
                    if ($operation !== 'create') {
                        return;
                    }

                    $set('value', Str::slug((string) $state));
                }),
            TextInput::make('value')
                ->label(__('configurator.fields.value'))
                ->required()
                ->maxLength(255),
            TextInput::make('position')
                ->label(__('configurator.fields.position'))
                ->numeric()
                ->default(0)
                ->required(),
            Toggle::make('is_default')
                ->label(__('configurator.fields.is_default'))
                ->default(false),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('label')
                    ->label(__('configurator.fields.label'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('value')
                    ->label(__('configurator.fields.value'))
                    ->toggleable(),
                IconColumn::make('is_default')
                    ->label(__('configurator.fields.is_default'))
                    ->boolean(),
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
                    ->tooltip(__('configurator.actions.edit_value')),
                DeleteAction::make()
                    ->iconButton()
                    ->tooltip(__('configurator.actions.delete_value')),
            ], RecordActionsPosition::AfterColumns)
            ->recordActionsColumnLabel(__('configurator.fields.actions'));
    }

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord instanceof AttributeCollection
            && parent::canViewForRecord($ownerRecord, $pageClass);
    }
}
