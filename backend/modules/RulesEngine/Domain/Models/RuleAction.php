<?php

declare(strict_types=1);

namespace Modules\RulesEngine\Domain\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\RulesEngine\Domain\Enums\RuleActionType;
use Modules\RulesEngine\Infrastructure\Observers\RuleActionObserver;
use Modules\RulesEngine\Infrastructure\Persistence\Factories\RuleActionFactory;
use Modules\Shared\Domain\Concerns\HasModuleFactory;
use Modules\Shared\Domain\Concerns\HasPublicId;

/**
 * @property int $id
 * @property string $public_id
 * @property int $rule_id
 * @property RuleActionType $type
 * @property array<string, mixed> $payload
 * @property int $position
 * @property-read Rule $rule
 *
 * @method static Builder<static> ordered()
 */
#[ObservedBy([RuleActionObserver::class])]
class RuleAction extends Model
{
    /** @use HasModuleFactory<RuleActionFactory> */
    use HasModuleFactory;

    use HasPublicId;

    protected $table = 'rules_engine_rule_actions';

    protected $fillable = [
        'public_id',
        'rule_id',
        'type',
        'payload',
        'position',
    ];

    protected function casts(): array
    {
        return [
            'type' => RuleActionType::class,
            'payload' => 'array',
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
     * @param  Builder<RuleAction>  $query
     * @return Builder<RuleAction>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('position')->orderBy('id');
    }
}
