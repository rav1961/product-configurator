<?php

declare(strict_types=1);

namespace Modules\Users\Application\DTO;

use Spatie\LaravelData\Data;

final class ForgotPasswordData extends Data
{
    public function __construct(
        public string $email,
    ) {}
}
