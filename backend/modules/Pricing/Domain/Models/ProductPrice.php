<?php

declare(strict_types=1);

namespace Modules\Pricing\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Catalog\Domain\Models\Product;
use Modules\Pricing\Infrastructure\Persistence\Factories\ProductPriceFactory;
use Modules\Shared\Domain\Concerns\HasModuleFactory;

/**
 * @property int $id
 * @property int $product_id
 * @property int $amount
 * @property-read Product $product
 */
class ProductPrice extends Model
{
    /** @use HasModuleFactory<ProductPriceFactory> */
    use HasModuleFactory;

    protected $table = 'pricing_product_prices';

    protected $fillable = [
        'product_id',
        'amount',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
