<?php

declare(strict_types=1);

namespace Modules\Configurator\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Configurator\Domain\Exceptions\InvalidDependencyScopeException;
use Modules\Configurator\Domain\Models\Attribute;
use Modules\Configurator\Domain\Models\Dependency;
use Modules\Configurator\Domain\Models\Step;
use Modules\Shared\Domain\Enums\SelectionCondition;
use Tests\TestCase;

final class DependencyModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_dependency_links_product_source_and_target(): void
    {
        $dependency = Dependency::factory()->create();

        $this->assertSame($dependency->product_id, $dependency->product->id);
        $this->assertSame($dependency->source_attribute_id, $dependency->sourceAttribute->id);
        $this->assertSame($dependency->target_attribute_id, $dependency->targetAttribute->id);
        $this->assertSame($dependency->product_id, $dependency->sourceAttribute->step->product_id);
    }

    public function test_equals_condition_requires_value(): void
    {
        $this->expectException(InvalidDependencyScopeException::class);

        Dependency::factory()->create([
            'condition' => SelectionCondition::Equals,
            'condition_value' => null,
        ]);
    }

    public function test_source_and_target_must_belong_to_configured_product(): void
    {
        $sourceStep = Step::factory()->create();
        $targetStep = Step::factory()->create();
        $source = Attribute::factory()->for($sourceStep)->create();
        $target = Attribute::factory()->for($targetStep)->create();

        $this->expectException(InvalidDependencyScopeException::class);

        Dependency::factory()->create([
            'product_id' => $sourceStep->product_id,
            'source_attribute_id' => $source->id,
            'target_attribute_id' => $target->id,
        ]);
    }

    public function test_observer_rejects_invalid_dependency_on_update(): void
    {
        $dependency = Dependency::factory()->create();
        $foreignStep = Step::factory()->create();
        $foreignAttribute = Attribute::factory()->for($foreignStep)->create();

        $this->expectException(InvalidDependencyScopeException::class);

        $dependency->update([
            'target_attribute_id' => $foreignAttribute->id,
        ]);
    }

    public function test_deleting_product_cascades_dependencies(): void
    {
        $dependency = Dependency::factory()->create();

        $dependency->product->delete();

        $this->assertSame(0, Dependency::query()->count());
    }

    public function test_ordered_scope_sorts_by_position(): void
    {
        $dependency = Dependency::factory()->create(['position' => 5]);

        Dependency::factory()->create([
            'product_id' => $dependency->product_id,
            'position' => 1,
        ]);

        $positions = Dependency::query()
            ->where('product_id', $dependency->product_id)
            ->ordered()
            ->pluck('position')
            ->all();
        $this->assertSame([1, 5], $positions);
    }
}
