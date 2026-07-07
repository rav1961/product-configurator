<?php

declare(strict_types=1);

namespace Modules\Configurator\Application\DTO;

use Spatie\LaravelData\Data;

final class ConfigurationEvaluationData extends Data
{
    /**
     * @param  array<string, ConfigurationAttributeStateData>  $attributes
     */
    public function __construct(
        public string $productId,
        public array $attributes,
    ) {}

    /**
     * API shape: attributes keyed by public_id (ULID), not a numeric list.
     *
     * @return array{productId: string, attributes: array<string, array<string, mixed>>}
     */
    public function toResponseArray(): array
    {
        $attributes = [];

        foreach ($this->attributes as $id => $state) {
            $attributes[$id] = $state->toArray();
        }

        return [
            'productId' => $this->productId,
            'attributes' => $attributes,
        ];
    }
}
