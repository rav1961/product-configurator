<?php

declare(strict_types=1);

namespace Modules\RulesEngine\Domain\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Modules\Catalog\Domain\Models\Product;
use Modules\RulesEngine\Domain\Enums\MatchMode;
use Modules\RulesEngine\Infrastructure\Persistence\Factories\RuleFactory;
use Modules\Shared\Domain\Concerns\HasModuleFactory;
use Modules\Shared\Domain\Concerns\HasPublicId;

/**
 * @property int $id
 * @property string $public_id
 * @property int $product_id
 * @property string $name
 * @property string|null $description
 * @property MatchMode $groups_match_mode
 * @property int $position
 * @property bool $is_active
 * @property-read Product $product
 * @property-read Collection<int, RuleGroup> $groups
 * @property-read Collection<int, RuleAction> $actions
 *
 * @method static Builder<static> ordered()
 * @method static Builder<static> active()
 */
class Rule extends Model
{
    /** @use HasModuleFactory<RuleFactory> */
    use HasModuleFactory;

    use HasPublicId;

    protected $table = 'rules_engine_rules';

    protected $fillable = [
        'public_id',
        'product_id',
        'name',
        'description',
        'groups_match_mode',
        'position',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'groups_match_mode' => MatchMode::class,
            'position' => 'integer',
            'is_active' => 'boolean',
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
     * @return HasMany<RuleGroup, $this>
     */
    public function groups(): HasMany
    {
        return $this->hasMany(RuleGroup::class);
    }

    /**
     * @return HasMany<RuleAction, $this>
     */
    public function actions(): HasMany
    {
        return $this->hasMany(RuleAction::class);
    }

    /**
     * @param  Builder<Rule>  $query
     * @return Builder<Rule>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('position')->orderBy('id');
    }

    /**
     * @param  Builder<Rule>  $query
     * @return Builder<Rule>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
