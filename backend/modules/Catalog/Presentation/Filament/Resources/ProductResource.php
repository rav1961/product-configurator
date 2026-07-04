<?php

declare(strict_types=1);

namespace Modules\Catalog\Presentation\Filament\Resources;

use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Modules\Catalog\Domain\Enums\ProductStatus;
use Modules\Catalog\Domain\Models\Product;
use Modules\Catalog\Presentation\Filament\Resources\ProductResource\Pages\CreateProduct;
use Modules\Catalog\Presentation\Filament\Resources\ProductResource\Pages\EditProduct;
use Modules\Catalog\Presentation\Filament\Resources\ProductResource\Pages\ListProducts;
use Modules\Shared\Domain\Enums\MediaCollection;
use Modules\Shared\Domain\Enums\MediaConversion;
use Modules\Shared\Presentation\Filament\ProductRelationRegistrar;

final class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCube;

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): string
    {
        return __('products.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('products.navigation.label');
    }

    public static function getModelLabel(): string
    {
        return __('products.label.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('products.label.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('category_id')
                ->label(__('products.fields.category'))
                ->relationship('category', 'name')
                ->searchable()
                ->preload()
                ->nullable(),
            TextInput::make('name')
                ->label(__('products.fields.name'))
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
                ->label(__('products.fields.slug'))
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true),
            TextInput::make('sku')
                ->label(__('products.fields.sku'))
                ->maxLength(255)
                ->unique(ignoreRecord: true),
            Select::make('status')
                ->label(__('products.fields.status'))
                ->options(self::statusOptions())
                ->default(ProductStatus::Draft->value)
                ->required(),
            Toggle::make('is_configurable')
                ->label(__('products.fields.is_configurable'))
                ->helperText(__('products.fields.is_configurable_help')),
            Textarea::make('description')
                ->label(__('products.fields.description'))
                ->rows(5)
                ->columnSpanFull(),
            SpatieMediaLibraryFileUpload::make('cover')
                ->label(__('products.fields.cover'))
                ->collection(MediaCollection::Cover->value)
                ->image()
                ->maxFiles(1)
                ->columnSpanFull(),
            TextInput::make('position')
                ->label(__('products.fields.position'))
                ->numeric()
                ->default(0)
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('cover')
                    ->collection(MediaCollection::Cover->value)
                    ->conversion(MediaConversion::Thumb->value)
                    ->square(),
                TextColumn::make('name')
                    ->label(__('products.fields.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category.name')
                    ->label(__('products.fields.category'))
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('sku')
                    ->label(__('products.fields.sku'))
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('status')
                    ->label(__('products.fields.status'))
                    ->badge()
                    ->formatStateUsing(static fn (ProductStatus $state): string => $state->label())
                    ->color(static fn (ProductStatus $state): string => match ($state) {
                        ProductStatus::Draft => 'gray',
                        ProductStatus::Active => 'success',
                        ProductStatus::Archived => 'warning',
                    }),
                IconColumn::make('is_configurable')
                    ->label(__('products.fields.is_configurable'))
                    ->boolean()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('position')
                    ->label(__('products.fields.position'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label(__('products.fields.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('position')
            ->filters([
                SelectFilter::make('status')
                    ->label(__('products.fields.status'))
                    ->options(self::statusOptions()),
                TernaryFilter::make('is_configurable')
                    ->label(__('products.fields.is_configurable')),
                SelectFilter::make('category')
                    ->label(__('products.fields.category'))
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
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

    /**
     * @return array<string, string>
     */
    private static function statusOptions(): array
    {
        $options = [];

        foreach (ProductStatus::cases() as $status) {
            $options[$status->value] = $status->label();
        }

        return $options;
    }

    public static function getRelations(): array
    {
        return app(ProductRelationRegistrar::class)->all();
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'edit' => EditProduct::route('/{record}/edit'),
        ];
    }
}
