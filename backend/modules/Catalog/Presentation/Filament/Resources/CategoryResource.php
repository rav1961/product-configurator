<?php

declare(strict_types=1);

namespace Modules\Catalog\Presentation\Filament\Resources;

use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Modules\Catalog\Domain\Models\Category;
use Modules\Catalog\Presentation\Filament\Resources\CategoryResource\Pages\CreateCategory;
use Modules\Catalog\Presentation\Filament\Resources\CategoryResource\Pages\EditCategory;
use Modules\Catalog\Presentation\Filament\Resources\CategoryResource\Pages\ListCategories;
use Modules\Shared\Domain\Enums\MediaCollection;
use Modules\Shared\Domain\Enums\MediaConversion;

final class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): string
    {
        return __('catalog.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('catalog.navigation.label');
    }

    public static function getModelLabel(): string
    {
        return __('catalog.label.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('catalog.label.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label(__('catalog.fields.name'))
                ->required()
                ->maxLength(255)
                ->live(onBlur: true)
                ->afterStateUpdated(static function (string $operation, ?string $state, Set $set): void {
                    if ($operation !== 'create') {
                        return;
                    }

                    $set('slug', Str::slug((string) $state));
                }),
            TextInput::make('slug')
                ->label(__('catalog.fields.slug'))
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true),
            Textarea::make('description')
                ->label(__('catalog.fields.description'))
                ->rows(4)
                ->columnSpanFull(),
            SpatieMediaLibraryFileUpload::make('cover')
                ->label(__('products.fields.cover'))
                ->collection(MediaCollection::Cover->value)
                ->image()
                ->maxFiles(1)
                ->columnSpanFull(),
            TextInput::make('position')
                ->label(__('catalog.fields.position'))
                ->numeric()
                ->default(0)
                ->required(),
            Toggle::make('is_active')
                ->label(__('catalog.fields.is_active'))
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            SpatieMediaLibraryImageColumn::make('cover')
                ->collection(MediaCollection::Cover->value)
                ->conversion(MediaConversion::Thumb->value)
                ->square(),
            TextColumn::make('name')
                ->label(__('catalog.fields.name'))
                ->searchable()
                ->sortable(),
            TextColumn::make('slug')
                ->label(__('catalog.fields.slug'))
                ->searchable()
                ->toggleable(),
            IconColumn::make('is_active')
                ->label(__('catalog.fields.is_active'))
                ->boolean()
                ->sortable(),
            TextColumn::make('position')
                ->label(__('catalog.fields.position'))
                ->numeric()
                ->sortable(),
            TextColumn::make('updated_at')
                ->label(__('catalog.fields.updated_at'))
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ])
            ->defaultSort('position')
            ->filters([
                TernaryFilter::make('is_active')
                    ->label(__('catalog.fields.is_active')),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCategories::route('/'),
            'create' => CreateCategory::route('/create'),
            'edit' => EditCategory::route('/{record}/edit'),
        ];
    }
}
