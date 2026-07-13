<?php

declare(strict_types=1);

namespace Modules\Pricing\Presentation\Filament\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Modules\Catalog\Domain\Models\Product;
use Modules\Pricing\Domain\Models\ProductPrice;
use Modules\Shared\Domain\ValueObjects\Money;
use Modules\Shared\Presentation\Filament\Forms\MoneyAmountInput;

final class ProductPriceRelationManager extends RelationManager
{
    protected static string $relationship = 'price';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        if (! $ownerRecord instanceof Product || ! $ownerRecord->isConfigurable()) {
            return false;
        }

        return parent::canViewForRecord($ownerRecord, $pageClass);
    }

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('pricing.navigation.base_price');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            MoneyAmountInput::make('amount', __('pricing.fields.amount')),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('amount')
                    ->label(__('pricing.fields.amount'))
                    ->getStateUsing(fn (ProductPrice $record): string => Money::pln($record->amount)->toDecimal()),
            ])
            ->headerActions([
                CreateAction::make()
                    ->visible(function (): bool {
                        $owner = $this->getOwnerRecord();
                        assert($owner instanceof Product);

                        return ! $owner->price()->exists();
                    })
                    ->using(function (array $data): Model {
                        $owner = $this->getOwnerRecord();
                        assert($owner instanceof Product);

                        return ProductPrice::query()->create([
                            'product_id' => $owner->getKey(),
                            'amount' => MoneyAmountInput::parseOrFail('amount', (string) $data['amount'])->amountMinor,
                        ]);
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->iconButton()
                    ->tooltip(__('pricing.actions.edit'))
                    ->fillForm(fn (ProductPrice $record): array => [
                        'amount' => Money::pln($record->amount)->toDecimal(),
                    ])
                    ->using(function (ProductPrice $record, array $data): ProductPrice {
                        $record->update([
                            'amount' => MoneyAmountInput::parseOrFail('amount', (string) $data['amount'])->amountMinor,
                        ]);

                        return $record;
                    }),
                DeleteAction::make()
                    ->iconButton()
                    ->tooltip(__('pricing.actions.delete')),
            ], RecordActionsPosition::AfterColumns)
            ->recordActionsColumnLabel(__('pricing.fields.actions'));
    }
}
