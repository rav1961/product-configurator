<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Catalog;

use App\Actions\Catalog\ListActiveProductsAction;
use App\Data\Catalog\ProductListItemData;
use App\Http\Controllers\Controller;
use App\Models\Catalog\Product;
use App\Support\Http\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class ProductIndexController extends Controller
{
    public function __invoke(
        Request $request,
        ListActiveProductsAction $action,
    ): JsonResponse {
        $perPage = min(
            $request->integer('per_page', ListActiveProductsAction::DEFAULT_PER_PAGE),
            ListActiveProductsAction::MAX_PER_PAGE,
        );

        $products = $action->execute($perPage);

        return ApiResponse::paginated(
            $products,
            $products->getCollection()
                ->map(fn (Product $product): ProductListItemData => ProductListItemData::fromModel($product)),
        );
    }
}
