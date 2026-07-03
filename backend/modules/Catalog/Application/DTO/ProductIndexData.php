<?php

declare(strict_types=1);

namespace Modules\Catalog\Application\DTO;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

final class ProductIndexData extends Data
{
    public function __construct(
        #[MapInputName('category')]
        public ?string $categoryPublicId = null,
        #[MapInputName('configurable')]
        public ?bool $configurableOnly = null,
        #[MapInputName('per_page')]
        public int $perPage = 15,
    ) {}
}
