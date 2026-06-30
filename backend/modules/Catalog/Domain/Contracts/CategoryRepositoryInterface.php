<?php

declare(strict_types=1);

namespace Modules\Catalog\Domain\Contracts;

use Illuminate\Support\Collection;
use Modules\Catalog\Domain\Models\Category;

interface CategoryRepositoryInterface
{
    /**
     * @return Collection<int, Category>
     */
    public function listActiveOrdered(): Collection;
}
