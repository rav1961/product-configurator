<?php

declare(strict_types=1);

namespace Modules\Configurator\Domain\ValueObjects;

final readonly class ConfigurationSelection
{
    /**
     * @param  array<string, mixed>  $values  keyed by public_id
     */
    public function __construct(
        private array $values,
    ) {}

    public function get(string $attributePublicId): mixed
    {
        return $this->values[$attributePublicId] ?? null;
    }

    /**
     * @param  array<string, mixed>  $values
     */
    public static function fromArray(array $values): self
    {
        return new self($values);
    }

    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return $this->values;
    }

    /**
     * @return list<string>
     */
    public function keys(): array
    {
        return array_keys($this->values);
    }
}
