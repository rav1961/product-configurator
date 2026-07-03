<?php

declare(strict_types=1);

namespace Modules\Configurator\Domain\Enums;

enum AttributeType: string
{
    case Text = 'text';
    case Number = 'number';
    case Boolean = 'boolean';
    case Select = 'select';
    case MultiSelect = 'multiselect';

    public function label(): string
    {
        return match ($this) {
            self::Text => __('configurator.attribute_type.text'),
            self::Number => __('configurator.attribute_type.number'),
            self::Boolean => __('configurator.attribute_type.boolean'),
            self::Select => __('configurator.attribute_type.select'),
            self::MultiSelect => __('configurator.attribute_type.multiselect'),
        };
    }

    /**
     * @return list<self>
     */
    public static function optionTypes(): array
    {
        return [
            self::Select,
            self::MultiSelect,
        ];
    }

    public function hasOptions(): bool
    {
        return in_array($this, self::optionTypes(), true);
    }
}
