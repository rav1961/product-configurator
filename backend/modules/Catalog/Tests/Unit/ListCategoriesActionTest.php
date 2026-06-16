<?php

declare(strict_types=1);

namespace Modules\Catalog\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Catalog\Application\Actions\ListCategoriesAction;
use Modules\Catalog\Domain\Models\Category;
use Tests\TestCase;

final class ListCategoriesActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_execute_returns_only_active_categories_ordered_by_position_then_name(): void
    {
        Category::factory()->create(['name' => 'Zulu', 'position' => 30]);
        Category::factory()->create(['name' => 'Beta', 'position' => 10]);
        Category::factory()->create(['name' => 'Alpha', 'position' => 10]);

        Category::factory()->inactive()->create(['name' => 'Hidden', 'position' => 0]);

        $result = app(ListCategoriesAction::class)->execute();

        $this->assertCount(3, $result);
        $this->assertSame(
            ['Alpha', 'Beta', 'Zulu'],
            $result->pluck('name')->all(),
        );
    }
}
