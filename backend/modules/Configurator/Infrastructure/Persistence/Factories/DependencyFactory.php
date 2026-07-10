<?php

declare(strict_types=1);

namespace Modules\Configurator\Infrastructure\Persistence\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Configurator\Domain\Enums\DependencyAction;
use Modules\Configurator\Domain\Models\Attribute;
use Modules\Configurator\Domain\Models\Dependency;
use Modules\Configurator\Domain\Models\Step;
use Modules\Shared\Domain\Enums\SelectionCondition;

/**
 * @extends Factory<Dependency>
 */
final class DependencyFactory extends Factory
{
    protected $model = Dependency::class;

    public function configure(): DependencyFactory
    {
        return $this->afterMaking(function (Dependency $dependency): void {
            $attributes = $dependency->getAttributes();

            if (isset($attributes['source_attribute_id'], $attributes['target_attribute_id'])) {
                return;
            }

            $productId = $attributes['product_id'] ?? null;

            if ($productId !== null) {
                $step = Step::factory()->create([
                    'product_id' => $productId,
                ]);
            } else {
                $step = Step::factory()->create();
                $dependency->product_id = $step->product_id;
            }

            $source = Attribute::factory()->for($step)->create();
            $target = Attribute::factory()->for($step)->create();

            $dependency->source_attribute_id = $source->id;
            $dependency->target_attribute_id = $target->id;
        });
    }

    public function definition(): array
    {
        return [
            'public_id' => (string) Str::ulid(),
            'condition' => SelectionCondition::Equals,
            'condition_value' => 'red',
            'action' => DependencyAction::Show,
            'position' => fake()->numberBetween(0, 100),
        ];
    }

    public function whenSet(): static
    {
        return $this->state([
            'condition' => SelectionCondition::IsSet,
            'condition_value' => null,
        ]);
    }
}
