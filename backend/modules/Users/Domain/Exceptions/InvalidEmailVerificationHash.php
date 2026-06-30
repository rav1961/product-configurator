<?php

declare(strict_types=1);

namespace Modules\Users\Domain\Exceptions;

use RuntimeException;

final class InvalidEmailVerificationHash extends RuntimeException {}
