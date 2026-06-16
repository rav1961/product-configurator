<?php

declare(strict_types=1);

namespace Modules\Catalog\Domain\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Catalog\Domain\Enums\ProductStatus;
use Modules\Catalog\Infrastructure\Persistence\Factories\ProductFactory;
use Modules\Shared\Domain\Concerns\HasModuleFactory;
use Modules\Shared\Domain\Concerns\HasPublicId;

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
final class Product extends Model
{
    /** @use HasModuleFactory<ProductFactory> */
    use HasModuleFactory;

    use HasPublicId;

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
