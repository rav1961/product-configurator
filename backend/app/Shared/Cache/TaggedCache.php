<?php

declare(strict_types=1);

namespace App\Shared\Cache;

use Closure;
use Illuminate\Cache\TaggableStore;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\Cache;
use Throwable;

final class TaggedCache
{
    public function remember(
        CachePolicy $policy,
        string $key,
        Closure $callback,
    ): mixed {
        if (! $policy->enabled) {
            return $callback();
        }

        try {
            $ttlSeconds = $policy->ttlWithJitter();

            if ($ttlSeconds <= 0) {
                return $callback();
            }

            $repository = Cache::store($policy->store);

            if ($this->supportsTags($repository) && $policy->tags !== []) {
                return $repository
                    ->tags($policy->tags)
                    ->remember($key, $ttlSeconds, $callback);
            }

            if ($policy->requiresTaggableStore) {
                return $callback();
            }

            return $repository->remember($key, $ttlSeconds, $callback);
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

            if ($this->supportsTags($repository)) {
                $repository->tags($policy->tags)->flush();

                return;
            }

            if (! $policy->requiresTaggableStore) {
                $repository->flush();
            }
        } catch (Throwable) {
        }
    }

    private function supportsTags(Repository $repository): bool
    {
        return $repository->getStore() instanceof TaggableStore;
    }
}
