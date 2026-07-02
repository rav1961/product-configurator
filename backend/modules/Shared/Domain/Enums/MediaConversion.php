<?php

declare(strict_types=1);

namespace Modules\Shared\Domain\Enums;

enum MediaConversion: string
{
    case Thumb = 'thumb';
    case Preview = 'preview';
}
