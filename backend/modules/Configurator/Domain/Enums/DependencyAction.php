<?php

declare(strict_types=1);

namespace Modules\Configurator\Domain\Enums;

enum DependencyAction: string
{
    case Show = 'show';
    case Hide = 'hide';
    case Require = 'require';
    case Disable = 'disable';

    public function label(): string
    {
        return match ($this) {
            self::Show => __('configurator.dependency_action.show'),
            self::Hide => __('configurator.dependency_action.hide'),
            self::Require => __('configurator.dependency_action.require'),
            self::Disable => __('configurator.dependency_action.disable'),
        };
    }
}
