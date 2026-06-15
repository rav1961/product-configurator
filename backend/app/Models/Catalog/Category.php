<?php

declare(strict_types=1);

namespace App\Models\Catalog;

use App\Events\Catalog\CatalogChanged;
use App\Models\Concerns\HasPublicId;
use Database\Factories\Catalog\CategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory;

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

    protected static function booted(): void
    {
        self::saved(static function (self $category): void {
            CatalogChanged::dispatch(
                source: 'catalog.category.saved',
                publicId: $category->public_id,
            );
        });

        self::deleted(static function (self $category): void {
            CatalogChanged::dispatch(
                source: 'catalog.category.deleted',
                publicId: $category->public_id,
            );
        });
    }

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
}
