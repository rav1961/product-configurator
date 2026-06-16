<?php

declare(strict_types=1);

namespace Modules\Catalog\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Catalog\Domain\Enums\ProductStatus;
use Modules\Catalog\Infrastructure\Persistence\Factories\ProductFactory;
use Modules\Shared\Domain\Concerns\HasModuleFactory;
use Modules\Shared\Domain\Concerns\HasPublicId;

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
        'short_description',
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

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function isActive(): bool
    {
        return $this->getRawOriginal('status') === ProductStatus::Active->value;
    }
}
