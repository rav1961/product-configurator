<?php

declare(strict_types=1);

namespace Modules\Catalog\Domain\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Catalog\Infrastructure\Persistence\Factories\CategoryFactory;
use Modules\Shared\Domain\Concerns\HasModuleFactory;
use Modules\Shared\Domain\Concerns\HasPublicId;

/**
 * @property int $id
 * @property string $public_id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property int $position
 * @property bool $is_active
 */
final class Category extends Model
{
    /** @use HasModuleFactory<CategoryFactory> */
    use HasModuleFactory;

    use HasPublicId;

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
