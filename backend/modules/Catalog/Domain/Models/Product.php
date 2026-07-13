<?php

declare(strict_types=1);

namespace Modules\Catalog\Domain\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Modules\Catalog\Domain\Enums\ProductStatus;
use Modules\Catalog\Infrastructure\Persistence\Factories\ProductFactory;
use Modules\Configurator\Domain\Concerns\InteractsWithConfiguratorEntities;
use Modules\Configurator\Domain\Models\AttributeCollection;
use Modules\Configurator\Domain\Models\Dependency;
use Modules\Configurator\Domain\Models\Step;
use Modules\Pricing\Domain\Concerns\InteractsWithPricing;
use Modules\RulesEngine\Domain\Concerns\InteractsWithRules;
use Modules\RulesEngine\Domain\Models\Rule;
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
 * @property bool $is_configurable
 * @property int $position
 * @property-read Category|null $category
 * @property-read Collection<int, Step> $steps
 * @property-read Collection<int, AttributeCollection> $attributeCollections
 * @property-read Collection<int, Dependency> $dependencies
 * @property-read Collection<int, Rule> $rules
 */
class Product extends Model implements HasMedia
{
    use HasConfiguredMedia;

    /** @use HasModuleFactory<ProductFactory> */
    use HasModuleFactory;

    use HasPublicId;
    use InteractsWithConfiguratorEntities;
    use InteractsWithPricing;
    use InteractsWithRules;
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
        'is_configurable',
        'position',
    ];

    protected function casts(): array
    {
        return [
            'status' => ProductStatus::class,
            'is_configurable' => 'boolean',
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

    public function isConfigurable(): bool
    {
        return $this->is_configurable;
    }

    /**
     * @param  Builder<Product>  $query
     * @return Builder<Product>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', ProductStatus::Active->value);
    }

    /**
     * @param  Builder<Product>  $query
     * @return Builder<Product>
     */
    public function scopeConfigurable(Builder $query): Builder
    {
        return $query->where('is_configurable', true);
    }
}
