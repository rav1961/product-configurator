<?php

declare(strict_types=1);

namespace Modules\Configurator\Infrastructure\Persistence\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Configurator\Domain\Models\Attribute;
use Modules\Configurator\Domain\Models\AttributeCollection;
use Modules\Configurator\Domain\Models\AttributeValue;

/**
 * @extends Factory<AttributeValue>
 */
class AttributeValueFactory extends Factory
{
    protected $model = AttributeValue::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $label = Str::title(fake()->unique()->word());

        return [
            'public_id' => (string) Str::ulid(),
            'attribute_id' => Attribute::factory(),
            'collection_id' => null,
            'label' => $label,
            'value' => Str::slug($label),
            'position' => fake()->numberBetween(0, 100),
            'is_default' => false,
        ];
    }

    public function forCollection(AttributeCollection $collection): static
    {
        return $this->state([
            'attribute_id' => null,
            'collection_id' => $collection->id,
        ]);
    }

    public function default(): static
    {
        return $this->state([
            'is_default' => true,
        ]);
    }
}
