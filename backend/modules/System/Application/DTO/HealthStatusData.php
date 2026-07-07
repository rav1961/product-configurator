<?php

declare(strict_types=1);

namespace Modules\System\Application\DTO;

use Spatie\LaravelData\Data;

final class HealthStatusData extends Data
{
    public function __construct(
        public string $status,
        public string $timestamp,
        public ?string $environment = null,
    ) {}
}
