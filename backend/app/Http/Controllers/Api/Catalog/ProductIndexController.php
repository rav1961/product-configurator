<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Catalog;

use App\Actions\Catalog\ListActiveProductsPayloadAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Catalog\ProductIndexRequest;
use App\Shared\Http\ApiResponse;
use Illuminate\Http\JsonResponse;

final class ProductIndexController extends Controller
{
    public function __invoke(
        ProductIndexRequest $request,
        ListActiveProductsPayloadAction $action,
    ): JsonResponse {
        return ApiResponse::payload(
            $action->execute($request->filters())
        );
    }
}
