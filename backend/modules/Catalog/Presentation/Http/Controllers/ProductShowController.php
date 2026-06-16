<?php

declare(strict_types=1);

namespace Modules\Catalog\Presentation\Http\Controllers;

use Modules\Catalog\Application\Actions\GetProductAction;
use Modules\Catalog\Application\DTO\ProductData;
use Modules\Shared\Presentation\Http\Controllers\ApiController;

final class ProductShowController extends ApiController
{
    public function __invoke(
        string $productId,
        GetProductAction $action,
    ): ProductData {
        return ProductData::fromModel($action->execute($productId));
    }
}
