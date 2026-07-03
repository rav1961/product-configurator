<?php

declare(strict_types=1);

namespace Modules\Configurator\Domain\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Catalog\Domain\Models\Product;
use Modules\Configurator\Domain\Enums\DependencyAction;
use Modules\Configurator\Domain\Enums\DependencyCondition;
use Modules\Configurator\Infrastructure\Observers\DependencyObserver;
use Modules\Configurator\Infrastructure\Persistence\Factories\DependencyFactory;
use Modules\Shared\Domain\Concerns\HasModuleFactory;
use Modules\Shared\Domain\Concerns\HasPublicId;

/**
 * @property int $id
 * @property string $public_id
 * @property int $product_id
 * @property int $source_attribute_id
 * @property int $target_attribute_id
 * @property DependencyCondition $condition
 * @property string|null $condition_value
 * @property DependencyAction $action
 * @property int $position
 * @property-read Product $product
 * @property-read Attribute $sourceAttribute
 * @property-read Attribute $targetAttribute
 */
#[ObservedBy([DependencyObserver::class])]
final class Dependency extends Model
{
    /** @use HasModuleFactory<DependencyFactory> */
    use HasModuleFactory;

    use HasPublicId;

    protected $table = 'configurator_dependencies';

    protected $fillable = [
        'public_id',
        'product_id',
        'source_attribute_id',
        'condition',
        'condition_value',
        'target_attribute_id',
        'action',
        'position',
    ];

    protected function casts(): array
    {
        return [
            'condition' => DependencyCondition::class,
            'action' => DependencyAction::class,
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
     * @return BelongsTo<Attribute, $this>
     */
    public function sourceAttribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class, 'source_attribute_id');
    }

    /**
     * @return BelongsTo<Attribute, $this>
     */
    public function targetAttribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class, 'target_attribute_id');
    }

    /**
     * @param  Builder<Dependency>  $query
     * @return Builder<Dependency>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('position')->orderBy('id');
    }
}
