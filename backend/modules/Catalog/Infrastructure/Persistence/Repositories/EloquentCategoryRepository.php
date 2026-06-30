<?php

declare(strict_types=1);

namespace Modules\Catalog\Infrastructure\Persistence\Repositories;

use Illuminate\Support\Collection;
use Modules\Catalog\Domain\Contracts\CategoryRepositoryInterface;
use Modules\Catalog\Domain\Models\Category;

final class EloquentCategoryRepository implements CategoryRepositoryInterface
{
    /**
     * @return Collection<int, Category>
     */
    public function listActiveOrdered(): Collection
    {
        return Category::query()
            ->active()
            ->orderBy('position')
            ->orderBy('name')
            ->get();
    }
}
