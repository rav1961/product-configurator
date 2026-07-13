<?php

declare(strict_types=1);

namespace Modules\Pricing\Presentation\Filament\Resource;

use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\Catalog\Domain\Models\Product;
use Modules\Pricing\Domain\Models\ProductPrice;
use Modules\Pricing\Presentation\Filament\Resource\ProductPriceResource\Pages\CreateProductPrice;
use Modules\Pricing\Presentation\Filament\Resource\ProductPriceResource\Pages\EditProductPrice;
use Modules\Pricing\Presentation\Filament\Resource\ProductPriceResource\Pages\ListProductPrices;
use Modules\Shared\Domain\ValueObjects\Money;
use Modules\Shared\Presentation\Filament\Forms\MoneyAmountInput;

final class ProductPriceResource extends Resource
{
    protected static ?string $model = ProductPrice::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): string
    {
        return __('pricing.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('pricing.navigation.label');
    }

    public static function getModelLabel(): string
    {
        return __('pricing.label.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('pricing.label.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('product_id')
                ->label(__('pricing.fields.product'))
                ->searchable()
                ->preload()
                ->required()
                ->native(false)
                ->disabled(fn (string $operation): bool => $operation === 'edit')
                ->dehydrated(fn (string $operation): bool => $operation === 'create')
                ->options(static function (?ProductPrice $record): array {
                    $query = Product::query()->configurable()->orderBy('name');

                    if ($record !== null) {
                        $query->where(static function (Builder $builder) use ($record): void {
                            $builder
                                ->whereDoesntHave('price')
                                ->orWhere('id', $record->product_id);
                        });
                    } else {
                        $query->whereDoesntHave('price');
                    }

                    return $query
                        ->get()
                        ->mapWithKeys(static fn (Product $product): array => [
                            $product->id => self::productOptionLabel($product),
                        ])
                        ->all();
                }),
            MoneyAmountInput::make('amount', __('pricing.fields.amount'))
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')
                    ->label(__('pricing.fields.product'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('product.sku')
                    ->label(__('products.fields.sku'))
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('amount')
                    ->label(__('pricing.fields.amount'))
                    ->sortable()
                    ->getStateUsing(static fn (ProductPrice $record): string => Money::pln($record->amount)->toDecimal()),
                IconColumn::make('product.is_configurable')
                    ->label(__('products.fields.is_configurable'))
                    ->boolean()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label(__('catalog.fields.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('product.name')
            ->filters([
                TernaryFilter::make('configurable')
                    ->label(__('products.fields.is_configurable'))
                    ->queries(
                        true: static fn (Builder $query): Builder => $query->whereHas(
                            'product',
                            static fn (Builder $productQuery): Builder => $productQuery->where('is_configurable', true),
                        ),
                        false: static fn (Builder $query): Builder => $query->whereHas(
                            'product',
                            static fn (Builder $productQuery): Builder => $productQuery->where('is_configurable', false),
                        ),
                    ),
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('product');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProductPrices::route('/'),
            'create' => CreateProductPrice::route('/create'),
            'edit' => EditProductPrice::route('/{record}/edit'),
        ];
    }

    private static function productOptionLabel(Product $product): string
    {
        if ($product->sku === null || $product->sku === '') {
            return $product->name;
        }

        return "{$product->name} ({$product->sku})";
    }
}
