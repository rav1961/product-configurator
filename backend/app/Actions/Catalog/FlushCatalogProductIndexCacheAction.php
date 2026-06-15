<?php

declare(strict_types=1);

namespace App\Actions\Catalog;

use App\Enums\Cache\CachePolicyName;
use App\Shared\Cache\CachePolicyResolver;
use App\Shared\Cache\TaggedCache;

final readonly class FlushCatalogProductIndexCacheAction
{
    public function __construct(
        private CachePolicyResolver $policies,
        private TaggedCache $cache,
    ) {}

    public function execute(): void
    {
        $this->cache->flush(
            $this->policies->resolve(CachePolicyName::CatalogProductsIndex),
        );
    }
}
