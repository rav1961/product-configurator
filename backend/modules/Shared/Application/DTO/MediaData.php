<?php

declare(strict_types=1);

namespace Modules\Shared\Application\DTO;

use Modules\Shared\Domain\Enums\MediaConversion;
use Modules\Shared\Domain\Enums\MediaProfile;
use Spatie\LaravelData\Data;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

final class MediaData extends Data
{
    public function __construct(
        public string $name,
        public int $position,
        public string $src,
        public string $srcset,
        public string $thumb,
    ) {}

    public static function fromMedia(Media $media, MediaProfile $profile): self
    {
        $responsive = $profile->responsiveConversion();

        return new self(
            name: $media->name,
            position: $media->order_column ?? 0,
            src: $media->getUrl($responsive->value),
            srcset: $media->getSrcset($responsive->value),
            thumb: $media->getUrl(MediaConversion::Thumb->value),
        );
    }
}
