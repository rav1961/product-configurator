<?php

declare(strict_types=1);

namespace App\Shared\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

final class ApiResponse
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public static function payload(
        array $payload
    ): JsonResponse {
        return response()->json($payload);
    }

    /**
     * @param  Collection<int, mixed>  $data
     */
    public static function collection(
        Collection $data
    ): JsonResponse {
        return response()->json([
            'data' => $data,
        ]);
    }

    /**
     * @param  Collection<int, mixed>  $data
     */
    public static function paginated(
        LengthAwarePaginator $paginator,
        Collection $data
    ): JsonResponse {
        return response()->json([
            'data' => $data->values(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'from' => $paginator->firstItem(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'to' => $paginator->lastItem(),
                'total' => $paginator->total(),
            ],
        ]);
    }
}
