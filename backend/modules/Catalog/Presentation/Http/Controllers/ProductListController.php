<?php

declare(strict_types=1);

namespace Modules\Catalog\Presentation\Http\Controllers;

use Modules\Catalog\Application\Actions\ListProductsAction;
use Modules\Catalog\Application\DTO\ProductData;
use Modules\Catalog\Application\DTO\ProductIndexData;
use Modules\Catalog\Domain\Models\Product;
use Modules\Catalog\Presentation\Http\Requests\ProductIndexRequest;
use Modules\Shared\Presentation\Http\Controllers\ApiController;
use Spatie\LaravelData\PaginatedDataCollection;

final class ProductListController extends ApiController
{
    /**
     * @return PaginatedDataCollection<int, ProductData>
     */
    public function __invoke(
        ProductIndexRequest $request,
        ListProductsAction $action,
    ): PaginatedDataCollection {
        $validated = $request->validated();

        $products = $action->execute(
            ProductIndexData::from($request->validated()),
        );

        return ProductData::collect(
            $products->through(
                static fn (Product $product): ProductData => ProductData::fromModel($product),
            ),
            PaginatedDataCollection::class,
        );
    }
}
