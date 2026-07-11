<?php

declare(strict_types=1);

namespace Modules\RulesEngine\Domain\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Modules\RulesEngine\Domain\Enums\MatchMode;
use Modules\RulesEngine\Infrastructure\Persistence\Factories\RuleGroupFactory;
use Modules\Shared\Domain\Concerns\HasModuleFactory;
use Modules\Shared\Domain\Concerns\HasPublicId;

/**
 * @property int $id
 * @property string $public_id
 * @property int $rule_id
 * @property MatchMode $conditions_match_mode
 * @property int $position
 * @property-read Rule $rule
 * @property-read Collection<int, RuleCondition> $conditions
 *
 * @method static Builder<static> ordered()
 */
class RuleGroup extends Model
{
    /** @use HasModuleFactory<RuleGroupFactory> */
    use HasModuleFactory;

    use HasPublicId;

    protected $table = 'rules_engine_rule_groups';

    protected $fillable = [
        'public_id',
        'rule_id',
        'conditions_match_mode',
        'position',
    ];

    protected function casts(): array
    {
        return [
            'conditions_match_mode' => MatchMode::class,
            'position' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<Rule, $this>
     */
    public function rule(): BelongsTo
    {
        return $this->belongsTo(Rule::class);
    }

    /**
     * @return HasMany<RuleCondition, $this>
     */
    public function conditions(): HasMany
    {
        return $this->hasMany(RuleCondition::class);
    }

    /**
     * @param  Builder<RuleGroup>  $query
     * @return Builder<RuleGroup>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('position')->orderBy('id');
    }
}
