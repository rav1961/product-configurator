<?php

declare(strict_types=1);

namespace Modules\Configurator\Domain\ValueObjects;

final readonly class ConfigurationSelection
{
    /**
     * @param  array<string, mixed>  $values  keyed by attribute.key
     */
    public function __construct(
        private array $values,
    ) {}

    public function get(string $attributeKey): mixed
    {
        return $this->values[$attributeKey] ?? null;
    }

    /**
     * @param  array<string, mixed>  $values
     */
    public static function fromArray(array $values): self
    {
        return new self($values);
    }
}
