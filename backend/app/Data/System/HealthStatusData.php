<?php

declare(strict_types=1);

namespace App\Data\System;

use Spatie\LaravelData\Data;

final class HealthStatusData extends Data
{
    public function __construct(
        public string $status,
        public string $app,
        public string $environment,
        public string $timestamp,
    ) {}
}
