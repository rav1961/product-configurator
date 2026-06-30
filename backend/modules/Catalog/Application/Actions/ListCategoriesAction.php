<?php

declare(strict_types=1);

namespace Modules\Catalog\Application\Actions;

use Illuminate\Support\Collection;
use Modules\Catalog\Domain\Contracts\CategoryRepositoryInterface;
use Modules\Catalog\Domain\Models\Category;

final readonly class ListCategoriesAction
{
    public function __construct(
        private CategoryRepositoryInterface $categories,
    ) {}

    /**
     * @return Collection<int, Category>
     */
    public function execute(): Collection
    {
        return $this->categories->listActiveOrdered();
    }
}
