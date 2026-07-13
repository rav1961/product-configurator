<?php

declare(strict_types=1);

namespace Modules\Pricing\Presentation\Filament\Resource\ProductPriceResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Pricing\Presentation\Filament\Resource\ProductPriceResource;

final class ListProductPrices extends ListRecords
{
    protected static string $resource = ProductPriceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
