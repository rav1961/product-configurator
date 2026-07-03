<?php

declare(strict_types=1);

namespace Modules\Configurator\Infrastructure\Persistence\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Configurator\Domain\Enums\AttributeType;
use Modules\Configurator\Domain\Models\Attribute;
use Modules\Configurator\Domain\Models\Step;

/**
 * @extends Factory<Attribute>
 */
final class AttributeFactory extends Factory
{
    protected $model = Attribute::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = Str::title(fake()->unique()->words(2, true));

        return [
            'public_id' => (string) Str::ulid(),
            'step_id' => Step::factory(),
            'name' => $name,
            'key' => Str::slug($name),
            'type' => fake()->randomElement(AttributeType::cases()),
            'is_required' => fake()->boolean(30),
        ];
    }

    public function required(): self
    {
        return $this->state([
            'is_required' => true,
        ]);
    }

    public function select(): self
    {
        return $this->state([
            'type' => AttributeType::Select,
        ]);
    }
}
