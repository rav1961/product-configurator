<?php

declare(strict_types=1);

namespace Modules\Catalog\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Catalog\Domain\Models\Category;
use Tests\TestCase;

final class CategoryApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_only_active_categories(): void
    {
        Category::factory()->count(2)->create();
        Category::factory()->inactive()->create();

        $response = $this->getJson('/api/categories');

        $response
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure([
                'data' => [
                    ['id', 'name', 'slug', 'description', 'position'],
                ],
            ]);
    }

    public function test_index_sorts_by_position_then_name(): void
    {
        Category::factory()->create(['name' => 'Zulu', 'position' => 30]);
        Category::factory()->create(['name' => 'Beta', 'position' => 10]);
        Category::factory()->create(['name' => 'Alpha', 'position' => 10]);

        $this->getJson('/api/categories')
            ->assertOk()
            ->assertJsonPath('data.0.name', 'Alpha')
            ->assertJsonPath('data.1.name', 'Beta')
            ->assertJsonPath('data.2.name', 'Zulu');
    }
}
