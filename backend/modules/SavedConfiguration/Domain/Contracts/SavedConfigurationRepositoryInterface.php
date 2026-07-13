<?php

declare(strict_types=1);

namespace Modules\SavedConfiguration\Domain\Contracts;

use Modules\SavedConfiguration\Domain\Models\SavedConfiguration;
use Modules\Users\Domain\Models\User;

interface SavedConfigurationRepositoryInterface
{
    /**
     * @param  array<string, mixed>  $selection
     * @param  array<string, mixed>  $price
     * @param  array<string, mixed>  $effects
     */
    public function create(
        User $user,
        string $productPublicId,
        array $selection,
        array $price,
        array $effects,
    ): SavedConfiguration;

    public function findOwnedByPublicId(
        User $user,
        string $publicId,
    ): SavedConfiguration;
}
