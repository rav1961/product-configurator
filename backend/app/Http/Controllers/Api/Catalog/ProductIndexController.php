<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Catalog;

use App\Actions\Catalog\ListActiveProductsAction;
use App\Data\Catalog\ProductListItemData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Catalog\ProductIndexRequest;
use App\Models\Catalog\Product;
use App\Support\Http\ApiResponse;
use Illuminate\Http\JsonResponse;

final class ProductIndexController extends Controller
{
    public function __invoke(
        ProductIndexRequest $request,
        ListActiveProductsAction $action,
    ): JsonResponse {
        $products = $action->execute($request->perPage());

        return ApiResponse::paginated(
            $products,
            $products->getCollection()
                ->map(fn (Product $product): ProductListItemData => ProductListItemData::fromModel($product)),
        );
    }
}
