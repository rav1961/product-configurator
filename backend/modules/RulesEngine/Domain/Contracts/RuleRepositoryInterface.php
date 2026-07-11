<?php

declare(strict_types=1);

namespace Modules\RulesEngine\Domain\Contracts;

use Illuminate\Support\Collection;
use Modules\RulesEngine\Domain\Models\Rule;

interface RuleRepositoryInterface
{
    /**
     * @return Collection<int, Rule>
     */
    public function listOrderedForProduct(int $productId): Collection;

    /**
     * @return Collection<int, Rule>
     */
    public function listActiveOrderedForProductPublicId(string $productPublicId): Collection;

    public function findByPublicId(string $publicId): Rule;
}
