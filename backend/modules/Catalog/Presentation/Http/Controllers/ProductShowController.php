<?php

declare(strict_types=1);

namespace Modules\Catalog\Presentation\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Modules\Catalog\Application\Actions\GetProductAction;
use Modules\Catalog\Application\DTO\ProductData;
use Modules\Shared\Presentation\Http\Controllers\ApiController;

final class ProductShowController extends ApiController
{
    public function __invoke(
        string $product,
        GetProductAction $action,
    ): JsonResponse {
        return response()->json([
            'data' => ProductData::fromModel($action->execute($product)),
        ]);
    }
}
