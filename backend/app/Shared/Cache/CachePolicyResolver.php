<?php

declare(strict_types=1);

namespace App\Shared\Cache;

use App\Enums\Cache\CachePolicyName;
use InvalidArgumentException;

final class CachePolicyResolver
{
    public function resolve(CachePolicyName $policyName): CachePolicy
    {
        $defaults = config('cache-policy.defaults', []);
        $policy = config("cache-policy.policies.{$policyName->value}");

        if (! is_array($defaults)) {
            $defaults = [];
        }

        if (! is_array($policy)) {
            throw new InvalidArgumentException(
                "Cache policy '{$policyName->value}' is not configured."
            );
        }

        $config = array_replace($defaults, $policy);
        $tags = $config['tags'] ?? [];

        if (! is_array($tags)) {
            $tags = [];
        }

        $store = $config['store'] ?? null;

        if (! is_string($store) || $store === '') {
            $store = (string) config('cache.default', 'redis');
        }

        return new CachePolicy(
            name: $policyName,
            enabled: (bool) ($config['enabled'] ?? true),
            store: $store,
            ttlSeconds: (int) ($config['ttl_seconds'] ?? 600),
            jitterSeconds: (int) ($config['jitter_seconds'] ?? 0),
            version: (string) ($config['version'] ?? 'v1'),
            tags: array_values(array_filter(
                $tags,
                static fn (mixed $tag): bool => is_string($tag) && $tag !== '',
            )),
            requiresTaggableStore: (bool) ($config['requires_taggable_store'] ?? true),
        );
    }
}
