<?php

declare(strict_types=1);

namespace Modules\SavedConfiguration\Infrastructure\Persistence\Repositories;

use Modules\Catalog\Domain\Models\Product;
use Modules\SavedConfiguration\Domain\Contracts\SavedConfigurationRepositoryInterface;
use Modules\SavedConfiguration\Domain\Enums\SavedConfigurationStatus;
use Modules\SavedConfiguration\Domain\Models\SavedConfiguration;
use Modules\Users\Domain\Models\User;

final class EloquentSavedConfigurationRepository implements SavedConfigurationRepositoryInterface
{
    public function create(
        User $user,
        string $productPublicId,
        array $selection,
        array $price,
        array $effects,
    ): SavedConfiguration {
        $product = Product::query()
            ->where('public_id', $productPublicId)
            ->firstOrFail();

        return SavedConfiguration::query()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'status' => SavedConfigurationStatus::Draft,
            'selection' => $selection,
            'price' => $price,
            'effects' => $effects,
        ]);
    }

    public function findOwnedByPublicId(
        User $user,
        string $publicId,
    ): SavedConfiguration {
        return SavedConfiguration::query()
            ->where('user_id', $user->id)
            ->where('public_id', $publicId)
            ->firstOrFail();
    }
}
