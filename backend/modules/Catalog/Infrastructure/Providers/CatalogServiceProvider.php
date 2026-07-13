<?php

declare(strict_types=1);

namespace Modules\Catalog\Infrastructure\Providers;

use Filament\Panel;
use Illuminate\Support\Facades\Gate;
use Modules\Catalog\Domain\Contracts\CategoryRepositoryInterface;
use Modules\Catalog\Domain\Contracts\ProductRepositoryInterface;
use Modules\Catalog\Domain\Models\Category;
use Modules\Catalog\Domain\Models\Product;
use Modules\Catalog\Infrastructure\Persistence\Repositories\EloquentCategoryRepository;
use Modules\Catalog\Infrastructure\Persistence\Repositories\EloquentProductRepository;
use Modules\Catalog\Presentation\Filament\Policies\CategoryPolicy;
use Modules\Catalog\Presentation\Filament\Policies\ProductPolicy;
use Modules\Catalog\Presentation\Filament\Resources\CategoryResource;
use Modules\Catalog\Presentation\Filament\Resources\ProductResource;
use Modules\Shared\Infrastructure\Providers\ModuleServiceProvider;
use Modules\Shared\Presentation\Filament\Enums\PanelName;

final class CatalogServiceProvider extends ModuleServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CategoryRepositoryInterface::class, EloquentCategoryRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, EloquentProductRepository::class);

        Panel::configureUsing(static function (Panel $panel): void {
            if ($panel->getId() !== PanelName::Admin->value) {
                return;
            }

            $panel->resources([
                CategoryResource::class,
                ProductResource::class,
            ]);
        });
    }

    public function boot(): void
    {
        parent::boot();

        Gate::policy(Category::class, CategoryPolicy::class);
        Gate::policy(Product::class, ProductPolicy::class);
    }
}
