<?php

declare(strict_types=1);

namespace Modules\Configurator\Presentation\Filament\Resources;

use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Catalog\Presentation\Filament\Resources\ProductResource;
use Modules\Configurator\Domain\Models\Step;
use Modules\Configurator\Presentation\Filament\RelationManagers\AttributesRelationManager;
use Modules\Configurator\Presentation\Filament\Resources\StepResource\Pages\EditStep;

final class StepResource extends Resource
{
    protected static ?string $model = Step::class;

    protected static ?string $parentResource = ProductResource::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQueueList;

    public static function getModelLabel(): string
    {
        return __('configurator.label.step.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('configurator.label.step.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label(__('configurator.fields.name'))
                ->required()
                ->maxLength(255),
            TextInput::make('position')
                ->label(__('configurator.fields.position'))
                ->numeric()
                ->default(0)
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
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
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            AttributesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'edit' => EditStep::route('/{record}/edit'),
        ];
    }
}
