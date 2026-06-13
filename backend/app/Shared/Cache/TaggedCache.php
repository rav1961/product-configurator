<?php

declare(strict_types=1);

namespace App\Shared\Cache;

use Closure;
use Illuminate\Cache\TaggableStore;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\Cache;
use Random\RandomException;
use Throwable;

final class TaggedCache
{
    /**
     * @throws RandomException
     */
    public function remember(
        CachePolicy $policy,
        string $key,
        Closure $callback,
    ): mixed {
        if (! $policy->enabled) {
            return $callback();
        }

        $ttlSeconds = $policy->ttlWithJitter();

        if ($ttlSeconds <= 0) {
            return $callback();
        }

        try {
            $repository = Cache::store($policy->store);

            if ($this->supportsTags($repository) && $policy->tags !== []) {
                return $repository
                    ->tags($policy->tags)
                    ->remember($key, $ttlSeconds, $callback);
            }

            return $repository->remember(
                $this->fallbackKey($key, $policy->tags),
                $ttlSeconds,
                $callback,
            );
        } catch (Throwable) {
            return $callback();
        }
    }

    public function flush(CachePolicy $policy): void
    {
        if ($policy->tags === []) {
            return;
        }

        try {
            $repository = Cache::store($policy->store);

            if (! $this->supportsTags($repository)) {
                return;
            }

            $repository->tags($policy->tags)->flush();
        } catch (Throwable) {
        }
    }

    private function supportsTags(Repository $repository): bool
    {
        return $repository->getStore() instanceof TaggableStore;
    }

    /**
     * @param  list<string>  $tags
     */
    private function fallbackKey(string $key, array $tags): string
    {
        if ($tags === []) {
            return $key;
        }

        return implode(':', $tags).':'.$key;
    }
}
