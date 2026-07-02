<?php

declare(strict_types=1);

namespace Modules\Shared\Application\Services;

use Modules\Shared\Domain\Enums\MediaConversion;
use Modules\Shared\Domain\Enums\MediaProfile;
use Modules\Shared\Domain\Media\MediaConversionDefinition;
use Spatie\MediaLibrary\HasMedia;

final class MediaConversionRegistrar
{
    public function register(HasMedia $model, MediaProfile $profile): void
    {
        foreach ($profile->definitions() as [$conversion, $definition]) {
            $this->addConversion($model, $conversion, $definition);
        }
    }

    private function addConversion(
        HasMedia $model,
        MediaConversion $conversion,
        MediaConversionDefinition $definition,
    ): void {
        $builder = $model->addMediaConversion($conversion->value);

        $builder->format($definition->format);

        if ($definition->width !== null && $definition->height !== null) {
            $builder->fit($definition->fit, $definition->width, $definition->height);
        } elseif ($definition->width !== null) {
            $builder->width($definition->width);
        }

        if ($definition->responsive) {
            $builder->withResponsiveImages();
        }

        if (! $definition->queued) {
            $builder->nonQueued();
        }
    }
}
