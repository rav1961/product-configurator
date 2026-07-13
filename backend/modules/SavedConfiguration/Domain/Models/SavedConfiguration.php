<?php

declare(strict_types=1);

namespace Modules\SavedConfiguration\Domain\Models;

use DateTimeImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Catalog\Domain\Models\Product;
use Modules\SavedConfiguration\Domain\Enums\SavedConfigurationStatus;
use Modules\SavedConfiguration\Infrastructure\Persistence\Factories\SavedConfigurationFactory;
use Modules\Shared\Domain\Concerns\HasModuleFactory;
use Modules\Shared\Domain\Concerns\HasPublicId;
use Modules\Users\Domain\Models\User;

/**
 * @property int $id
 * @property string $public_id
 * @property int $user_id
 * @property int $product_id
 * @property SavedConfigurationStatus $status
 * @property array<string, mixed> $selection
 * @property array<string, mixed> $price
 * @property array<string, mixed> $effects
 * @property DateTimeImmutable $created_at
 * @property DateTimeImmutable $updated_at
 * @property-read User $user
 * @property-read Product $product
 */
final class SavedConfiguration extends Model
{
    /** @use HasModuleFactory<SavedConfigurationFactory> */
    use HasModuleFactory;

    use HasPublicId;

    protected $table = 'saved_configurations';

    protected $fillable = [
        'user_id',
        'product_id',
        'status',
        'selection',
        'price',
        'effects',
    ];

    protected function casts(): array
    {
        return [
            'status' => SavedConfigurationStatus::class,
            'selection' => 'array',
            'price' => 'array',
            'effects' => 'array',
            'created_at' => 'immutable_datetime',
            'updated_at' => 'immutable_datetime',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
