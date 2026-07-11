<?php

declare(strict_types=1);

namespace Modules\RulesEngine\Domain\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Configurator\Domain\Models\Attribute;
use Modules\RulesEngine\Infrastructure\Observers\RuleConditionObserver;
use Modules\RulesEngine\Infrastructure\Persistence\Factories\RuleConditionFactory;
use Modules\Shared\Domain\Concerns\HasModuleFactory;
use Modules\Shared\Domain\Concerns\HasPublicId;
use Modules\Shared\Domain\Enums\SelectionCondition;

/**
 * @property int $id
 * @property string $public_id
 * @property int $rule_group_id
 * @property int $source_attribute_id
 * @property SelectionCondition $condition
 * @property string|null $condition_value
 * @property int $position
 * @property-read RuleGroup $ruleGroup
 * @property-read Attribute $sourceAttribute
 *
 * @method static Builder<static> ordered()
 */
#[ObservedBy([RuleConditionObserver::class])]
class RuleCondition extends Model
{
    /** @use HasModuleFactory<RuleConditionFactory> */
    use HasModuleFactory;

    use HasPublicId;

    protected $table = 'rules_engine_rule_conditions';

    protected $fillable = [
        'public_id',
        'rule_group_id',
        'source_attribute_id',
        'condition',
        'condition_value',
        'position',
    ];

    protected function casts(): array
    {
        return [
            'condition' => SelectionCondition::class,
            'position' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<RuleGroup, $this>
     */
    public function ruleGroup(): BelongsTo
    {
        return $this->belongsTo(RuleGroup::class);
    }

    /**
     * @return BelongsTo<Attribute, $this>
     */
    public function sourceAttribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class, 'source_attribute_id');
    }

    /**
     * @param  Builder<RuleCondition>  $query
     * @return Builder<RuleCondition>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('position')->orderBy('id');
    }
}
