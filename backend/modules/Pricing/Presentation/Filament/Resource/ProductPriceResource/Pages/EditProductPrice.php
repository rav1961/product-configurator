<?php

declare(strict_types=1);

namespace Modules\Pricing\Presentation\Filament\Resource\ProductPriceResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Pricing\Presentation\Filament\Resource\ProductPriceResource;
use Modules\Shared\Domain\ValueObjects\Money;
use Modules\Shared\Presentation\Filament\Forms\MoneyAmountInput;

final class EditProductPrice extends EditRecord
{
    protected static string $resource = ProductPriceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (isset($data['amount'])) {
            $data['amount'] = Money::pln((int) $data['amount'])->toDecimal();
        }

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['amount'] = MoneyAmountInput::parseOrFail('amount', (string) $data['amount'])->amountMinor;

        return $data;
    }
}
