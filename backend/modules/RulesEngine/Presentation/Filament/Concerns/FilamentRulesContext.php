<?php

declare(strict_types=1);

namespace Modules\RulesEngine\Presentation\Filament\Concerns;

use LogicException;
use Modules\Catalog\Domain\Models\Product;
use Modules\RulesEngine\Domain\Models\Rule;
use Modules\RulesEngine\Domain\Models\RuleGroup;

final class FilamentRulesContext
{
    public static function productId(object $livewire): int
    {
        if (method_exists($livewire, 'getOwnerRecord')) {
            $owner = $livewire->getOwnerRecord();

            if ($owner instanceof Product) {
                return $owner->getKey();
            }

            if ($owner instanceof Rule) {
                return (int) $owner->product_id;
            }

            if ($owner instanceof RuleGroup) {
                return (int) $owner->loadMissing('rule')->rule->product_id;
            }
        }

        if (method_exists($livewire, 'getParentRecord')) {
            $parent = $livewire->getParentRecord();

            if ($parent instanceof Product) {
                return $parent->getKey();
            }

            if ($parent instanceof Rule) {
                return (int) $parent->product_id;
            }
        }

        if (method_exists($livewire, 'getRecord') && $livewire->getRecord() instanceof Rule) {
            return (int) $livewire->getRecord()->product_id;
        }

        throw new LogicException('Cannot resolve product ID from Filament rules context.');
    }

    public static function ruleId(object $livewire): int
    {
        if (method_exists($livewire, 'getOwnerRecord')) {
            $owner = $livewire->getOwnerRecord();

            if ($owner instanceof Rule) {
                return $owner->getKey();
            }

            if ($owner instanceof RuleGroup) {
                return (int) $owner->rule_id;
            }
        }

        if (method_exists($livewire, 'getParentRecord') && $livewire->getParentRecord() instanceof Rule) {
            return $livewire->getParentRecord()->getKey();
        }

        if (method_exists($livewire, 'getRecord') && $livewire->getRecord() instanceof Rule) {
            return $livewire->getRecord()->getKey();
        }

        throw new LogicException('Cannot resolve rule ID from Filament rules context.');
    }

    /**
     * @return array{product: int, rule: int}
     */
    public static function ruleRouteParameters(object $livewire): array
    {
        return [
            'product' => self::productId($livewire),
            'rule' => self::ruleId($livewire),
        ];
    }
}
