<?php

declare(strict_types=1);

namespace Modules\Configurator\Domain\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Configurator\Domain\Exceptions\InvalidAttributeValueOwnershipException;
use Modules\Configurator\Infrastructure\Persistence\Factories\AttributeValueFactory;
use Modules\Shared\Domain\Concerns\HasModuleFactory;
use Modules\Shared\Domain\Concerns\HasPublicId;

/**
 * @property int $id
 * @property string $public_id
 * @property int|null $attribute_id
 * @property int|null $collection_id
 * @property string $label
 * @property string $value
 * @property int $position
 * @property bool $is_default
 * @property-read Attribute|null $attribute
 * @property-read AttributeCollection|null $collection
 */
final class AttributeValue extends Model
{
    /** @use HasModuleFactory<AttributeValueFactory> */
    use HasModuleFactory;

    use HasPublicId;

    protected $table = 'configurator_attribute_values';

    protected $fillable = [
        'public_id',
        'attribute_id',
        'collection_id',
        'label',
        'value',
        'position',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'position' => 'integer',
            'is_default' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        AttributeValue::saving(static function (AttributeValue $attributeValue): void {
            $hasAttribute = $attributeValue->attribute_id !== null;
            $hasCollection = $attributeValue->collection_id !== null;

            if ($hasAttribute === $hasCollection) {
                throw InvalidAttributeValueOwnershipException::mustBelongToExactlyOneOwner();
            }
        });
    }

    /**
     * @return BelongsTo<Attribute, $this>
     */
    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class);
    }

    /**
     * @return BelongsTo<AttributeCollection, $this>
     */
    public function collection(): BelongsTo
    {
        return $this->belongsTo(AttributeCollection::class, 'collection_id');
    }

    /**
     * @param  Builder<AttributeValue>  $query
     * @return Builder<AttributeValue>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('position')->orderBy('label');
    }
}
