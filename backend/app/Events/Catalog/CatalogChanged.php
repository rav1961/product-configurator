<?php

namespace App\Events\Catalog;

use Illuminate\Foundation\Events\Dispatchable;

final readonly class CatalogChanged
{
    use Dispatchable;

    public function __construct(
        public string $source,
        public ?string $publicId = null,
    ) {}
}
