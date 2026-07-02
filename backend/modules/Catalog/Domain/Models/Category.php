<?php

declare(strict_types=1);

namespace Modules\Catalog\Domain\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Catalog\Infrastructure\Persistence\Factories\CategoryFactory;
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
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property int $position
 * @property bool $is_active
 */
final class Category extends Model implements HasMedia
{
    use HasConfiguredMedia;

    /** @use HasModuleFactory<CategoryFactory> */
    use HasModuleFactory;

    use HasPublicId;
    use RegistersDefaultMediaCollection;

    protected $table = 'catalog_categories';

    protected $fillable = [
        'public_id',
        'name',
        'slug',
        'description',
        'position',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'position' => 'integer',
        ];
    }

    protected function mediaProfile(): MediaProfile
    {
        return MediaProfile::CategoryCover;
    }

    public function registerMediaCollections(): void
    {
        $this->registerDefaultMediaCollection(MediaCollection::Cover);
    }

    /**
     * @return HasMany<Product, $this>
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * @param  Builder<Category>  $query
     * @return Builder<Category>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
