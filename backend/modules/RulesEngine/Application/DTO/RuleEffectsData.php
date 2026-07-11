<?php

declare(strict_types=1);

namespace Modules\RulesEngine\Application\DTO;

use Spatie\LaravelData\Data;

final class RuleEffectsData extends Data
{
    /**
     * @param  array<int, RuleModifierEffectData>  $modifiers
     * @param  array<int, RuleOverrideEffectData>  $overrides
     * @param  array<int, RuleExcludedOptionEffectData>  $excludedOptions
     * @param  array<int, RuleMessageEffectData>  $messages
     */
    public function __construct(
        public array $modifiers = [],
        public array $overrides = [],
        public array $excludedOptions = [],
        public array $messages = [],
    ) {}
}
