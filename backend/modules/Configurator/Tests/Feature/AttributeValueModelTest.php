<?php

declare(strict_types=1);

namespace Modules\Configurator\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Configurator\Domain\Exceptions\InvalidAttributeValueOwnershipException;
use Modules\Configurator\Domain\Models\Attribute;
use Modules\Configurator\Domain\Models\AttributeCollection;
use Modules\Configurator\Domain\Models\AttributeValue;
use Tests\TestCase;

final class AttributeValueModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_value_cannot_belong_to_both_attribute_and_collection(): void
    {
        $this->expectException(InvalidAttributeValueOwnershipException::class);

        AttributeValue::factory()->create([
            'attribute_id' => Attribute::factory(),
            'collection_id' => AttributeCollection::factory(),
        ]);
    }

    public function test_value_must_have_an_owner(): void
    {
        $this->expectException(InvalidAttributeValueOwnershipException::class);

        AttributeValue::factory()->create([
            'attribute_id' => null,
            'collection_id' => null,
        ]);
    }

    public function test_attribute_uses_collection_flag(): void
    {
        $collection = AttributeCollection::factory()->create();

        $attribute = Attribute::factory()->create([
            'collection_id' => $collection->id,
        ]);

        $this->assertTrue($attribute->usesCollection());
    }
}
