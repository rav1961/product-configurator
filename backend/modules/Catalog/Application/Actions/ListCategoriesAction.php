<?php

declare(strict_types=1);

namespace Modules\Catalog\Application\Actions;

use Illuminate\Support\Collection;
use Modules\Catalog\Domain\Models\Category;

final class ListCategoriesAction
{
    public function execute(): Collection
    {
        return Category::query()
            ->active()
            ->orderBy('position')
            ->orderBy('name')
            ->get();
    }
}
