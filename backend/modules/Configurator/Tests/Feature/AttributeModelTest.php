<?php

declare(strict_types=1);

namespace Modules\Configurator\Tests\Feature;

use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Configurator\Domain\Enums\AttributeType;
use Modules\Configurator\Domain\Models\Attribute;
use Modules\Configurator\Domain\Models\Step;
use Tests\TestCase;

final class AttributeModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_attribute_type_is_cast_to_enum(): void
    {
        $attribute = Attribute::factory()->select()->create();

        $this->assertSame(AttributeType::Select, $attribute->type);
        $this->assertTrue($attribute->type->hasOptions());
    }

    public function test_key_is_unique_per_step(): void
    {
        $step = Step::factory()->create();

        Attribute::factory()->for($step)->create([
            'key' => 'frame-color',
        ]);

        $this->expectException(UniqueConstraintViolationException::class);

        Attribute::factory()->for($step)->create([
            'key' => 'frame-color',
        ]);
    }

    public function test_same_key_is_allowed_on_different_steps(): void
    {
        Attribute::factory()->create(['key' => 'frame-color']);
        Attribute::factory()->create(['key' => 'frame-color']);

        $this->assertSame(2, Attribute::query()->where('key', 'frame-color')->count());
    }

    public function test_ordered_scope_sorts_by_position_then_name(): void
    {
        $step = Step::factory()->create();

        Attribute::factory()->for($step)->create(['name' => 'Beta', 'position' => 1]);
        Attribute::factory()->for($step)->create(['name' => 'Alpha', 'position' => 0]);

        $names = Attribute::query()
            ->where('step_id', $step->id)
            ->ordered()
            ->pluck('name')
            ->all();

        $this->assertSame(['Alpha', 'Beta'], $names);
    }
}
