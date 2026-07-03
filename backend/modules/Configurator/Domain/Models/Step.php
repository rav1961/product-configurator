<?php

declare(strict_types=1);

namespace Modules\Configurator\Domain\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Catalog\Domain\Models\Product;
use Modules\Configurator\Infrastructure\Persistence\Factories\StepFactory;
use Modules\Shared\Domain\Concerns\HasModuleFactory;
use Modules\Shared\Domain\Concerns\HasPublicId;

/**
 * @property int $id
 * @property string $public_id
 * @property int $product_id
 * @property string $name
 * @property int $position
 * @property-read Product $product
 */
final class Step extends Model
{
    /** @use HasModuleFactory<StepFactory> */
    use HasModuleFactory;

    use HasPublicId;

    protected $table = 'configurator_steps';

    protected $fillable = [
        'public_id',
        'product_id',
        'name',
        'position',
    ];

    protected function casts(): array
    {
        return [
            'position' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * @param  Builder<Step>  $query
     * @return Builder<Step>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('position')->orderBy('name');
    }
}
