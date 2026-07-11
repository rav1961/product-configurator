<?php

declare(strict_types=1);

namespace Modules\RulesEngine\Infrastructure\Persistence\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Modules\RulesEngine\Domain\Contracts\RuleGraphRepositoryInterface;
use Modules\RulesEngine\Domain\Models\Rule;

class EloquentRuleGraphRepository implements RuleGraphRepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function buildActiveForProductPublicId(string $productPublicId): Collection
    {
        return Rule::query()
            ->whereHas(
                'product',
                fn (Builder $query): Builder => $query->where('public_id', $productPublicId),
            )
            ->active()
            ->with([
                'groups' => fn ($query) => $query->ordered(),
                'groups.conditions' => fn ($query) => $query->ordered(),
                'groups.conditions.sourceAttribute',
                'actions' => fn ($query) => $query->ordered(),
            ])
            ->ordered()
            ->get();
    }
}
