<?php

declare(strict_types=1);

return [
    'catalog' => [
        'products' => [
            'index' => [
                'cache' => [
                    'enabled' => env('CATALOG_PRODUCTS_INDEX_CACHE_ENABLED', true),
                    'ttl_seconds' => (int) env('CATALOG_PRODUCTS_INDEX_CACHE_TTL_SECONDS', 600),
                    'jitter_seconds' => (int) env('CATALOG_PRODUCTS_INDEX_CACHE_JITTER_SECONDS', 120),
                    'tags' => ['catalog.products'],
                ],
            ],
        ],
    ],
];
