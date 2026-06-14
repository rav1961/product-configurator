<?php

declare(strict_types=1);

namespace App\Data\Catalog;

use Spatie\LaravelData\Data;

final class ProductIndexFilters extends Data
{
    public const DEFAULT_PAGE = 1;

    public const DEFAULT_PER_PAGE = 24;

    public const MAX_PER_PAGE = 24;

    public function __construct(
        public int $page = self::DEFAULT_PAGE,
        public int $perPage = self::DEFAULT_PER_PAGE,
        public ?string $queryText = null,
    ) {}

    public function hasSearch(): bool
    {
        return $this->queryText !== null
            && $this->queryText !== '';
    }
}
