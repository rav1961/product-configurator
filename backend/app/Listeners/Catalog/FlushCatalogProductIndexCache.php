<?php

declare(strict_types=1);

namespace App\Listeners\Catalog;

use App\Actions\Catalog\FlushCatalogProductIndexCacheAction;
use App\Events\Catalog\CatalogChanged;

final readonly class FlushCatalogProductIndexCache
{
    public function __construct(
        private FlushCatalogProductIndexCacheAction $flushCatalogProductIndexCache,
    ) {}

    public function handle(CatalogChanged $event): void
    {
        $this->flushCatalogProductIndexCache->execute();
    }
}
