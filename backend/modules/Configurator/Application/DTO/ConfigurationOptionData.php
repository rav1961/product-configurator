<?php

declare(strict_types=1);

namespace Modules\Configurator\Application\DTO;

use Modules\Configurator\Domain\Models\AttributeValue;
use Spatie\LaravelData\Data;

final class ConfigurationOptionData extends Data
{
    public function __construct(
        public string $id,
        public string $label,
        public string $value,
        public bool $isDefault,
    ) {}

    public static function fromModel(AttributeValue $value): self
    {
        return new self(
            id: $value->public_id,
            label: $value->label,
            value: $value->value,
            isDefault: $value->is_default,
        );
    }
}
