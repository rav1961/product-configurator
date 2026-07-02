<?php

declare(strict_types=1);

namespace Modules\Shared\Domain\Enums;

use Modules\Shared\Domain\Media\MediaConversionDefinition;

enum MediaProfile: string
{
    case CategoryCover = 'category_cover';
    case ProductCover = 'product_cover';
    case ConfiguratorSwatch = 'configurator_swatch';
    case ConfiguratorIllustration = 'configurator_illustration';

    public function responsiveConversion(): MediaConversion
    {
        return MediaConversion::Preview;
    }

    /**
     * @return list<array{0: MediaConversion, 1: MediaConversionDefinition}>
     */
    public function definitions(): array
    {
        return match ($this) {
            self::CategoryCover => [
                [MediaConversion::Thumb, MediaConversionDefinition::crop(480, 270)],
                [MediaConversion::Preview, MediaConversionDefinition::width(1200, responsive: true)],
            ],
            self::ProductCover => [
                [MediaConversion::Thumb, MediaConversionDefinition::crop(400, 400)],
                [MediaConversion::Preview, MediaConversionDefinition::width(800, responsive: true)],
            ],
            self::ConfiguratorSwatch => [
                [MediaConversion::Thumb, MediaConversionDefinition::crop(64, 64)],
                [MediaConversion::Preview, MediaConversionDefinition::crop(128, 128, responsive: true)],
            ],
            self::ConfiguratorIllustration => [
                [MediaConversion::Thumb, MediaConversionDefinition::width(200)],
                [MediaConversion::Preview, MediaConversionDefinition::width(800, responsive: true)],
            ],
        };
    }
}
