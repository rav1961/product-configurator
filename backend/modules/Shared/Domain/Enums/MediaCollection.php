<?php

declare(strict_types=1);

namespace Modules\Shared\Domain\Enums;

enum MediaCollection: string
{
    case Cover = 'cover';
    case Swatch = 'swatch';
    case Illustration = 'illustration';
}
