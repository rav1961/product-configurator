<?php

declare(strict_types=1);

namespace App\Models\Catalog;

use App\Enums\Catalog\ProductStatus;
use App\Events\Catalog\CatalogChanged;
use App\Models\Concerns\HasPublicId;
use Database\Factories\Catalog\ProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    use HasPublicId;

    protected $table = 'catalog_products';

    protected $fillable = [
        'public_id',
        'category_id',
        'name',
        'slug',
        'sku',
        'short_description',
        'description',
        'status',
        'position',
    ];

    protected static function booted(): void
    {
        self::saved(static function (self $product): void {
            CatalogChanged::dispatch(
                source: 'catalog.product.saved',
                publicId: $product->public_id,
            );
        });

        self::deleted(static function (self $product): void {
            CatalogChanged::dispatch(
                source: 'catalog.product.deleted',
                publicId: $product->public_id,
            );
        });
    }

    protected function casts(): array
    {
        return [
            'status' => ProductStatus::class,
            'position' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function isActive(): bool
    {
        return $this->getRawOriginal('status') === ProductStatus::ACTIVE->value;
    }
}
