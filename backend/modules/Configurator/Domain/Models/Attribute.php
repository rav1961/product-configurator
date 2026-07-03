<?php

declare(strict_types=1);

namespace Modules\Configurator\Domain\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Configurator\Domain\Enums\AttributeType;
use Modules\Configurator\Infrastructure\Persistence\Factories\AttributeFactory;
use Modules\Shared\Domain\Concerns\HasModuleFactory;
use Modules\Shared\Domain\Concerns\HasPublicId;

/**
 * @property int $id
 * @property string $public_id
 * @property int $step_id
 * @property string $name
 * @property string $key
 * @property AttributeType $type
 * @property int $position
 * @property bool $is_required
 * @property-read Step $step
 */
final class Attribute extends Model
{
    /** @use HasModuleFactory<AttributeFactory> */
    use HasModuleFactory;

    use HasPublicId;

    protected $table = 'configurator_attributes';

    protected $fillable = [
        'public_id',
        'step_id',
        'name',
        'key',
        'type',
        'position',
        'is_required',
    ];

    protected function casts(): array
    {
        return [
            'type' => AttributeType::class,
            'position' => 'integer',
            'is_required' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Step, $this>
     */
    public function step(): BelongsTo
    {
        return $this->belongsTo(Step::class);
    }

    /**
     * @param  Builder<Attribute>  $query
     * @return Builder<Attribute>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('position')->orderBy('name');
    }
}
