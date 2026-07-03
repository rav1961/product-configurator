<?php

declare(strict_types=1);

namespace Modules\Configurator\Tests\Feature;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Catalog\Domain\Models\Product;
use Modules\Configurator\Domain\Contracts\StepRepositoryInterface;
use Modules\Configurator\Domain\Models\Step;
use Tests\TestCase;

final class StepRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_ordered_for_product(): void
    {
        $product = Product::factory()->create();

        Step::factory()->for($product)->create(['name' => 'Beta', 'position' => 2]);
        Step::factory()->for($product)->create(['name' => 'Alpha', 'position' => 1]);
        Step::factory()->create();

        $result = app(StepRepositoryInterface::class)->listOrderedForProduct($product->id);

        $this->assertCount(2, $result);
        $this->assertSame(['Alpha', 'Beta'], $result->pluck('name')->all());
    }

    public function test_find_by_public_id(): void
    {
        $step = Step::factory()->create();

        $found = app(StepRepositoryInterface::class)->findByPublicId($step->public_id);

        $this->assertTrue($found->is($step));
    }

    public function test_find_by_public_id_throws_when_unknown_public_id(): void
    {
        $this->expectException(ModelNotFoundException::class);

        app(StepRepositoryInterface::class)->findByPublicId('unknown-public-id');
    }
}
