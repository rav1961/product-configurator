<?php

declare(strict_types=1);

namespace Modules\RulesEngine\Infrastructure\Persistence\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Modules\RulesEngine\Domain\Contracts\RuleRepositoryInterface;
use Modules\RulesEngine\Domain\Models\Rule;

final class EloquentRuleRepository implements RuleRepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function listOrderedForProduct(int $productId): Collection
    {
        return Rule::query()
            ->where('product_id', $productId)
            ->ordered()
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function listActiveOrderedForProductPublicId(string $productPublicId): Collection
    {
        return Rule::query()
            ->whereHas(
                'product',
                fn (Builder $query): Builder => $query->where('public_id', $productPublicId),
            )
            ->active()
            ->ordered()
            ->get();
    }

    public function findByPublicId(string $publicId): Rule
    {
        return Rule::query()
            ->where('public_id', $publicId)
            ->firstOrFail();
    }
}
