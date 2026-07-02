<?php

declare(strict_types=1);

namespace Modules\Catalog\Domain\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Catalog\Domain\Enums\ProductStatus;
use Modules\Catalog\Infrastructure\Persistence\Factories\ProductFactory;
use Modules\Shared\Domain\Concerns\HasConfiguredMedia;
use Modules\Shared\Domain\Concerns\HasModuleFactory;
use Modules\Shared\Domain\Concerns\HasPublicId;
use Modules\Shared\Domain\Concerns\RegistersDefaultMediaCollection;
use Modules\Shared\Domain\Enums\MediaCollection;
use Modules\Shared\Domain\Enums\MediaProfile;
use Spatie\MediaLibrary\HasMedia;

/**
 * @property int $id
 * @property string $public_id
 * @property int|null $category_id
 * @property string $name
 * @property string $slug
 * @property string|null $sku
 * @property string|null $description
 * @property ProductStatus $status
 * @property int $position
 * @property-read Category|null $category
 */
final class Product extends Model implements HasMedia
{
    use HasConfiguredMedia;

    /** @use HasModuleFactory<ProductFactory> */
    use HasModuleFactory;

    use HasPublicId;
    use RegistersDefaultMediaCollection;

    protected $table = 'catalog_products';

    protected $fillable = [
        'public_id',
        'category_id',
        'name',
        'slug',
        'sku',
        'description',
        'status',
        'position',
    ];

    protected function casts(): array
    {
        return [
            'status' => ProductStatus::class,
            'position' => 'integer',
        ];
    }

    protected function mediaProfile(): MediaProfile
    {
        return MediaProfile::ProductCover;
    }

    public function registerMediaCollections(): void
    {
        $this->registerDefaultMediaCollection(MediaCollection::Cover);
    }

    /**
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function isActive(): bool
    {
        return $this->status === ProductStatus::Active;
    }

    /**
     * @param  Builder<Product>  $query
     * @return Builder<Product>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', ProductStatus::Active->value);
    }
}
