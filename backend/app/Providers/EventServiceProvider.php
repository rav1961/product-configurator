<?php

declare(strict_types=1);

namespace App\Providers;

use App\Events\Catalog\CatalogChanged;
use App\Listeners\Catalog\FlushCatalogProductIndexCache;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

final class EventServiceProvider extends ServiceProvider
{
    /**
     * @var array<class-string, list<class-string>>
     */
    protected $listen = [
        CatalogChanged::class => [
            FlushCatalogProductIndexCache::class,
        ],
    ];
}
