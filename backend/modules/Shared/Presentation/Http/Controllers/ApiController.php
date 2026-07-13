<?php

declare(strict_types=1);

namespace Modules\Shared\Presentation\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Spatie\LaravelData\Data;
use Symfony\Component\HttpFoundation\Response;

abstract class ApiController
{
    protected function responseJsonData(Data $data, int $status = Response::HTTP_OK): JsonResponse
    {
        return response()->json([
            'data' => $data->toArray(),
        ], $status);
    }
}
