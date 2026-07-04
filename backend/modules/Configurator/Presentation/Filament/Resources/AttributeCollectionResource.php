<?php

declare(strict_types=1);

namespace Modules\Configurator\Presentation\Filament\Resources;

use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;
use Modules\Catalog\Presentation\Filament\Resources\ProductResource;
use Modules\Configurator\Domain\Models\AttributeCollection;
use Modules\Configurator\Presentation\Filament\Concerns\FilamentOwnerContext;
use Modules\Configurator\Presentation\Filament\RelationManagers\CollectionValuesRelationManager;
use Modules\Configurator\Presentation\Filament\Resources\AttributeCollectionResource\EditAttributeCollection;

final class AttributeCollectionResource extends Resource
{
    protected static ?string $model = AttributeCollection::class;

    protected static ?string $parentResource = ProductResource::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function getModelLabel(): string
    {
        return __('configurator.label.collection.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('configurator.label.collection.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label(__('configurator.fields.name'))
                ->required()
                ->maxLength(255)
                ->live(onBlur: true)
                ->afterStateUpdated(static function (string $operation, ?string $state, Set $set): void {
                    if ($operation !== 'create') {
                        return;
                    }

                    $set('key', Str::slug((string) $state));
                }),
            TextInput::make('key')
                ->label(__('configurator.fields.key'))
                ->required()
                ->maxLength(255)
                ->scopedUnique(
                    AttributeCollection::class,
                    'key',
                    modifyQueryUsing: fn ($query, $livewire) => $query->where(
                        'product_id',
                        FilamentOwnerContext::productId($livewire),
                    ),
                ),
            TextInput::make('position')
                ->label(__('configurator.fields.position'))
                ->numeric()
                ->default(0)
                ->required(),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            CollectionValuesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'edit' => EditAttributeCollection::route('/{record}/edit'),
        ];
    }
}
