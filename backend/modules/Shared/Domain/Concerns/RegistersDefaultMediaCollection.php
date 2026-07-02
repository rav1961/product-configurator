<?php

declare(strict_types=1);

namespace Modules\Shared\Domain\Concerns;

use Modules\Shared\Domain\Enums\MediaCollection;
use Modules\Shared\Domain\Media\MediaMimeTypes;

trait RegistersDefaultMediaCollection
{
    /**
     * @param  list<string>|null  $mimeTypes  null = {@see MediaMimeTypes::images()}
     */
    protected function registerDefaultMediaCollection(
        MediaCollection $collection,
        bool $singleFile = true,
        ?array $mimeTypes = null,
    ): void {
        $mediaCollection = $this->addMediaCollection($collection->value)
            ->acceptsMimeTypes($mimeTypes ?? MediaMimeTypes::images());

        if ($singleFile) {
            $mediaCollection->singleFile();
        }
    }
}
