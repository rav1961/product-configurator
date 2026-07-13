<?php

declare(strict_types=1);

namespace Modules\Pricing\Presentation\Filament\Resource\ProductPriceResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Pricing\Presentation\Filament\Resource\ProductPriceResource;
use Modules\Shared\Presentation\Filament\Forms\MoneyAmountInput;

final class CreateProductPrice extends CreateRecord
{
    protected static string $resource = ProductPriceResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['amount'] = MoneyAmountInput::parseOrFail('amount', (string) $data['amount'])->amountMinor;

        return $data;
    }
}
