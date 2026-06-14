<?php

declare(strict_types=1);

return [
    'defaults' => [
        'enabled' => env('DOMAIN_CACHE_ENABLED', true),
        'store' => env('DOMAIN_CACHE_STORE', env('CACHE_STORE', 'redis')),
        'ttl_seconds' => (int) env('DOMAIN_CACHE_TTL_SECONDS', 600),
        'jitter_seconds' => (int) env('DOMAIN_CACHE_JITTER_SECONDS', 120),
        'requires_taggable_store' => true,
        'version' => env('DOMAIN_CACHE_VERSION', 'v1'),
    ],
    'policies' => [
        'catalog' => [
            'products' => [
                'index' => [
                    'tags' => [
                        'catalog.products',
                    ],
                ],
            ],
        ],
    ],
];
