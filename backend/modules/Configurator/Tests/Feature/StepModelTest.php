<?php

declare(strict_types=1);

namespace Modules\Configurator\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Catalog\Domain\Models\Product;
use Modules\Configurator\Domain\Models\Step;
use Tests\TestCase;

final class StepModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_ordered_scope_sorts_by_position_then_name(): void
    {
        $product = Product::factory()->create();

        Step::factory()->for($product)->create(['name' => 'Beta', 'position' => 1]);
        Step::factory()->for($product)->create(['name' => 'Alpha', 'position' => 0]);
        Step::factory()->for($product)->create(['name' => 'Gamma', 'position' => 1]);

        $names = Step::query()
            ->where('product_id', $product->id)
            ->ordered()
            ->pluck('name')
            ->all();

        $this->assertSame(['Alpha', 'Beta', 'Gamma'], $names);
    }

    public function test_deleting_product_cascades_steps(): void
    {
        $product = Product::factory()->create();

        Step::factory()->for($product)->count(2)->create();

        $product->delete();

        $this->assertSame(0, Step::query()->count());
    }
}
