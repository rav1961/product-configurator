<?php

declare(strict_types=1);

namespace Modules\Configurator\Tests\Feature;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Configurator\Domain\Contracts\AttributeValueRepositoryInterface;
use Modules\Configurator\Domain\Models\Attribute;
use Modules\Configurator\Domain\Models\AttributeCollection;
use Modules\Configurator\Domain\Models\AttributeValue;
use Tests\TestCase;

final class AttributeValueRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_ordered_for_attribute(): void
    {
        $attribute = Attribute::factory()->create();

        AttributeValue::factory()->for($attribute)->create(['label' => 'Beta', 'position' => 2]);
        AttributeValue::factory()->for($attribute)->create(['label' => 'Alpha', 'position' => 1]);
        AttributeValue::factory()->create();

        $result = app(AttributeValueRepositoryInterface::class)->listOrderedForAttribute($attribute->id);

        $this->assertCount(2, $result);
        $this->assertSame(['Alpha', 'Beta'], $result->pluck('label')->all());
    }

    public function test_list_ordered_for_collection(): void
    {
        $collection = AttributeCollection::factory()->create();

        AttributeValue::factory()->forCollection($collection)->create(['label' => 'Beta', 'position' => 2]);
        AttributeValue::factory()->forCollection($collection)->create(['label' => 'Alpha', 'position' => 1]);
        AttributeValue::factory()->create();

        $result = app(AttributeValueRepositoryInterface::class)->listOrderedForCollection($collection->id);

        $this->assertCount(2, $result);
        $this->assertSame(['Alpha', 'Beta'], $result->pluck('label')->all());
    }

    public function test_find_by_public_id(): void
    {
        $value = AttributeValue::factory()->create();

        $found = app(AttributeValueRepositoryInterface::class)->findByPublicId($value->public_id);

        $this->assertTrue($found->is($value));
    }

    public function test_find_by_public_id_throws_when_unknown(): void
    {
        $this->expectException(ModelNotFoundException::class);

        app(AttributeValueRepositoryInterface::class)->findByPublicId((string) Str::ulid());
    }
}
