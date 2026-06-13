<?php

declare(strict_types=1);

namespace App\Shared\Cache;

use InvalidArgumentException;

final class CachePolicyResolver
{
    public function resolve(string $policyName): CachePolicy
    {
        $config = config("domain-cache.policies.{$policyName}");

        if (! is_array($config)) {
            throw new InvalidArgumentException(
                "Cache policy '{$policyName}' is not configured."
            );
        }

        $tags = $config['tags'] ?? [];

        if (! is_array($tags)) {
            $tags = [];
        }

        return new CachePolicy(
            name: $policyName,
            enabled: (bool) ($config['enabled'] ?? true),
            store: (string) config('cache.default', 'redis'),
            ttlSeconds: (int) ($config['ttl_seconds'] ?? 0),
            jitterSeconds: (int) ($config['jitter_seconds'] ?? 0),
            tags: array_values(array_filter(
                $tags,
                fn (mixed $tag): bool => is_string($tag) && $tag !== '',
            )),
        );
    }
}
