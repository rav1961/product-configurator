<?php

declare(strict_types=1);

namespace App\Shared\Cache;

use Random\RandomException;

final readonly class CachePolicy
{
    public function __construct(
        public string $name,
        public bool $enabled,
        public string $store,
        public int $ttlSeconds,
        public int $jitterSeconds,
        public array $tags,
    ) {}

    /**
     * @throws RandomException
     */
    public function ttlWithJitter(): int
    {
        if ($this->ttlSeconds <= 0) {
            return 0;
        }

        if ($this->jitterSeconds <= 0) {
            return $this->ttlSeconds;
        }

        return $this->ttlSeconds + random_int(0, $this->jitterSeconds);
    }
}
