<?php

declare(strict_types=1);

namespace Modules\Users\Application\DTO;

use Spatie\LaravelData\Data;

final class ResetPasswordData extends Data
{
    public function __construct(
        public string $token,
        public string $email,
        public string $password,
    ) {}
}
