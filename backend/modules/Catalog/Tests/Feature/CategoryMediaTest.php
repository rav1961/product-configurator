<?php

declare(strict_types=1);

namespace Modules\Catalog\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Catalog\Application\DTO\CategoryData;
use Modules\Catalog\Domain\Models\Category;
use Modules\Shared\Domain\Enums\MediaCollection;
use Modules\Shared\Domain\Enums\MediaConversion;
use Tests\TestCase;

final class CategoryMediaTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_can_have_cover_image(): void
    {
        Storage::fake('public');

        $category = Category::factory()->create();

        $category->addMedia(UploadedFile::fake()->image('banner.jpg'))
            ->toMediaCollection(MediaCollection::Cover->value);

        $category->load('media');

        $this->assertCount(1, $category->getMedia(MediaCollection::Cover->value));
    }

    public function test_category_data_includes_responsive_urls(): void
    {
        Storage::fake('public');

        $category = Category::factory()->create();

        $category->addMedia(UploadedFile::fake()->image('banner.jpg'))
            ->usingName('Category banner')
            ->toMediaCollection(MediaCollection::Cover->value);

        $category->load('media');

        $data = CategoryData::fromModel($category);

        $this->assertNotNull($data->cover);
        $this->assertSame('Category banner', $data->cover->name);
        $this->assertNotSame('', $data->cover->src);
        $this->assertNotSame('', $data->cover->srcset);
        $this->assertStringContainsString(
            MediaConversion::Thumb->value,
            $data->cover->thumb,
        );
    }
}
