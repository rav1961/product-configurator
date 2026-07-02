<?php

declare(strict_types=1);

namespace Modules\Shared\Domain\Media;

final class MediaMimeTypes
{
    /**
     * @return list<string>
     */
    public static function images(): array
    {
        return [
            'image/jpeg',
            'image/png',
            'image/webp',
            'image/gif',
            'image/avif',
        ];
    }
}
