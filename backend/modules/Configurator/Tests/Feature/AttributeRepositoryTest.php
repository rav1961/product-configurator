<?php

declare(strict_types=1);

namespace Modules\Configurator\Tests\Feature;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Configurator\Domain\Contracts\AttributeRepositoryInterface;
use Modules\Configurator\Domain\Models\Attribute;
use Modules\Configurator\Domain\Models\Step;
use Tests\TestCase;

final class AttributeRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_ordered_for_step(): void
    {
        $step = Step::factory()->create();

        Attribute::factory()->for($step)->create(['name' => 'Beta', 'position' => 2]);
        Attribute::factory()->for($step)->create(['name' => 'Alpha', 'position' => 1]);
        Attribute::factory()->create();

        $result = app(AttributeRepositoryInterface::class)->listOrderedForStep($step->id);

        $this->assertCount(2, $result);
        $this->assertSame(['Alpha', 'Beta'], $result->pluck('name')->all());
    }

    public function test_find_by_public_id(): void
    {
        $attribute = Attribute::factory()->create();

        $found = app(AttributeRepositoryInterface::class)->findByPublicId($attribute->public_id);

        $this->assertTrue($found->is($attribute));
    }

    public function test_find_by_public_id_throws_when_unknown(): void
    {
        $this->expectException(ModelNotFoundException::class);

        app(AttributeRepositoryInterface::class)->findByPublicId((string) Str::ulid());
    }
}
