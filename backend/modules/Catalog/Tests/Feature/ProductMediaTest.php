<?php

declare(strict_types=1);

namespace Modules\Catalog\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Catalog\Application\DTO\ProductData;
use Modules\Catalog\Domain\Models\Product;
use Modules\Shared\Domain\Enums\MediaCollection;
use Modules\Shared\Domain\Enums\MediaConversion;
use Tests\TestCase;

final class ProductMediaTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_can_have_cover_image(): void
    {
        Storage::fake('public');

        $product = Product::factory()->active()->create();

        $product->addMedia(UploadedFile::fake()->image('photo.jpg'))
            ->toMediaCollection(MediaCollection::Cover->value);

        $product->load('media');

        $this->assertCount(1, $product->getMedia(MediaCollection::Cover->value));
    }

    public function test_product_data_includes_responsive_urls(): void
    {
        Storage::fake('public');

        $product = Product::factory()->active()->create();

        $product->addMedia(UploadedFile::fake()->image('hero.jpg'))
            ->usingName('Hero image')
            ->toMediaCollection(MediaCollection::Cover->value);

        $product->load('media');

        $data = ProductData::fromModel($product);

        $this->assertNotNull($data->cover);
        $this->assertSame('Hero image', $data->cover->name);
        $this->assertNotSame('', $data->cover->src);
        $this->assertNotSame('', $data->cover->srcset);
        $this->assertStringContainsString(
            MediaConversion::Thumb->value,
            $data->cover->thumb,
        );
    }
}
