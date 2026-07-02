<?php

declare(strict_types=1);

namespace Modules\Shared\Domain\Media;

use Spatie\Image\Enums\Fit;

final readonly class MediaConversionDefinition
{
    public const DEFAULT_FORMAT = 'webp';

    private function __construct(
        public Fit $fit,
        public ?int $width,
        public ?int $height,
        public string $format = self::DEFAULT_FORMAT,
        public bool $queued = false,
        public bool $responsive = false,
    ) {}

    public static function crop(
        int $width,
        int $height,
        string $format = self::DEFAULT_FORMAT,
        bool $responsive = false,
    ): self {
        return new self(
            fit: Fit::Crop,
            width: $width,
            height: $height,
            format: $format,
            responsive: $responsive,
        );
    }

    public static function width(
        int $width,
        string $format = self::DEFAULT_FORMAT,
        bool $responsive = false,
    ): self {
        return new self(
            fit: Fit::Max,
            width: $width,
            height: null,
            format: $format,
            responsive: $responsive,
        );
    }
}
