<?php

declare(strict_types=1);

namespace Modules\Catalog\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Catalog\Domain\Models\Category;
use Modules\Shared\Domain\Enums\MediaCollection;
use Modules\Users\Domain\Models\User;
use Tests\TestCase;

final class CategoryApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_guest_cannot_list_categories(): void
    {
        $this->getJson(route('api.categories.list'))->assertUnauthorized();
    }

    public function test_index_returns_only_active_categories(): void
    {
        Category::factory()->count(2)->create();
        Category::factory()->inactive()->create();

        $response = $this->actingAs($this->user)
            ->getJson(route('api.categories.list'));

        $response
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure([
                'data' => [
                    ['id', 'name', 'slug', 'description', 'position', 'cover'],
                ],
            ]);
    }

    public function test_index_returns_category_cover_with_responsive_urls(): void
    {
        Storage::fake('public');

        $category = Category::factory()->create();

        $category->addMedia(UploadedFile::fake()->image('banner.jpg'))
            ->usingName('Banner')
            ->toMediaCollection(MediaCollection::Cover->value);

        $this->actingAs($this->user)
            ->getJson(route('api.categories.list'))
            ->assertOk()
            ->assertJsonPath('data.0.cover.name', 'Banner')
            ->assertJsonStructure([
                'data' => [
                    [
                        'cover' => ['name', 'position', 'src', 'srcset', 'thumb'],
                    ],
                ],
            ]);
    }

    public function test_index_sorts_by_position_then_name(): void
    {
        Category::factory()->create(['name' => 'Zulu', 'position' => 30]);
        Category::factory()->create(['name' => 'Beta', 'position' => 10]);
        Category::factory()->create(['name' => 'Alpha', 'position' => 10]);

        $this->actingAs($this->user)
            ->getJson(route('api.categories.list'))
            ->assertOk()
            ->assertJsonPath('data.0.name', 'Alpha')
            ->assertJsonPath('data.1.name', 'Beta')
            ->assertJsonPath('data.2.name', 'Zulu');
    }
}
