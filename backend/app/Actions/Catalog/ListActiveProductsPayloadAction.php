<?php

declare(strict_types=1);

namespace App\Actions\Catalog;

use App\Data\Catalog\ProductIndexFilters;
use App\Data\Catalog\ProductListItemData;
use App\Enums\Cache\CachePolicyName;
use App\Models\Catalog\Product;
use App\Shared\Cache\CacheKeyBuilder;
use App\Shared\Cache\CachePolicyResolver;
use App\Shared\Cache\TaggedCache;
use Illuminate\Pagination\LengthAwarePaginator;
use JsonException;
use Random\RandomException;

final readonly class ListActiveProductsPayloadAction
{
    public function __construct(
        private ListActiveProductsAction $products,
        private CachePolicyResolver $policies,
        private CacheKeyBuilder $keys,
        private TaggedCache $cache,
    ) {}

    /**
     * @return array<string, mixed>
     *
     * @throws RandomException
     * @throws JsonException
     */
    public function execute(
        ProductIndexFilters $filters
    ): array {
        $policy = $this->policies->resolve(CachePolicyName::CatalogProductsIndex);

        $key = $this->keys->make($policy, [
            'page' => $filters->page,
            'per_page' => $filters->perPage,
            'q' => $filters->queryText,
        ]);

        $payload = $this->cache->remember(
            policy: $policy,
            key: $key,
            callback: fn (): array => $this->buildPayload($filters),
        );

        if (! is_array($payload)) {
            return $this->buildPayload($filters);
        }

        return $payload;
    }

    /**
     * @return array<string, mixed>
     */
    private function buildPayload(
        ProductIndexFilters $filters
    ): array {
        $paginator = $this->products->execute($filters);

        return [
            'data' => $this->items($paginator),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'from' => $paginator->firstItem(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'to' => $paginator->lastItem(),
                'total' => $paginator->total(),
            ],
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function items(LengthAwarePaginator $paginator): array
    {
        return $paginator
            ->getCollection()
            ->map(
                fn (Product $product): array => ProductListItemData::fromModel($product)->toArray()
            )
            ->values()
            ->all();
    }
}
