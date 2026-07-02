<?php

declare(strict_types=1);

namespace Modules\Shared\Domain\Concerns;

use Modules\Shared\Application\Services\MediaConversionRegistrar;
use Modules\Shared\Domain\Enums\MediaProfile;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

trait HasConfiguredMedia
{
    use InteractsWithMedia {
        InteractsWithMedia::registerMediaConversions as private registerSpatieMediaConversions;
    }

    abstract protected function mediaProfile(): MediaProfile;

    public function registerMediaConversions(?Media $media = null): void
    {
        app(MediaConversionRegistrar::class)->register($this, $this->mediaProfile());
    }
}
