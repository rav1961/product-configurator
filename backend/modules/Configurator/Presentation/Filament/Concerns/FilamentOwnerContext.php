<?php

declare(strict_types=1);

namespace Modules\Configurator\Presentation\Filament\Concerns;

use LogicException;
use Modules\Catalog\Domain\Models\Product;
use Modules\Configurator\Domain\Models\Attribute;
use Modules\Configurator\Domain\Models\Step;

final class FilamentOwnerContext
{
    public static function productId(object $livewire): int
    {
        if (method_exists($livewire, 'getOwnerRecord')) {
            $owner = $livewire->getOwnerRecord();
            assert($owner instanceof Product);

            return $owner->getKey();
        }

        if (method_exists($livewire, 'getParentRecord') && $livewire->getParentRecord() instanceof Product) {
            return $livewire->getParentRecord()->getKey();
        }

        if (method_exists($livewire, 'getRecord') && $livewire->getRecord() !== null) {
            return (int) $livewire->getRecord()->product_id;
        }

        throw new LogicException('Cannot resolve product ID from Filament livewire context.');
    }

    public static function step(object $livewire): Step
    {
        if (method_exists($livewire, 'getOwnerRecord')) {
            $owner = $livewire->getOwnerRecord();
            assert($owner instanceof Step);

            return $owner;
        }

        if (method_exists($livewire, 'getParentRecord') && $livewire->getParentRecord() instanceof Step) {
            return $livewire->getParentRecord();
        }

        if (method_exists($livewire, 'getRecord') && $livewire->getRecord() instanceof Attribute) {
            return $livewire->getRecord()->step;
        }

        throw new LogicException('Cannot resolve step from Filament livewire context.');
    }
}
