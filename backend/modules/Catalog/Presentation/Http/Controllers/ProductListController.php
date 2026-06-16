<?php

declare(strict_types=1);

namespace Modules\Catalog\Presentation\Http\Controllers;

use Modules\Catalog\Application\Actions\ListProductsAction;
use Modules\Catalog\Application\DTO\ProductData;
use Modules\Catalog\Domain\Models\Product;
use Modules\Catalog\Presentation\Http\Requests\ProductIndexRequest;
use Modules\Shared\Presentation\Http\Controllers\ApiController;
use Spatie\LaravelData\PaginatedDataCollection;

final class ProductListController extends ApiController
{
    public function __invoke(
        ProductIndexRequest $request,
        ListProductsAction $action,
    ): PaginatedDataCollection {
        $validated = $request->validated();

        $products = $action->execute(
            categoryPublicId: isset($validated['category'])
                ? (string) $validated['category']
                : null,
            perPage: isset($validated['per_page'])
                ? (int) $validated['per_page']
                : null,
        );

        return ProductData::collect(
            $products->through(
                static fn (Product $product): ProductData => ProductData::fromModel($product),
            ),
            PaginatedDataCollection::class,
        );
    }
}
