<?php

declare(strict_types=1);

namespace Modules\Catalog\Presentation\Http\Controllers;

use Modules\Catalog\Application\Actions\ListCategoriesAction;
use Modules\Catalog\Application\DTO\CategoryData;
use Modules\Catalog\Domain\Models\Category;
use Modules\Shared\Presentation\Http\Controllers\ApiController;
use Spatie\LaravelData\DataCollection;

final class CategoryListController extends ApiController
{
    public function __invoke(ListCategoriesAction $action): DataCollection
    {
        $categories = $action->execute()->map(
            static fn (Category $category): CategoryData => CategoryData::fromModel($category),
        );

        return CategoryData::collect(
            $categories,
            DataCollection::class,
        )->wrap('data');
    }
}
