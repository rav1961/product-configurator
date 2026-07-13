<?php

declare(strict_types=1);

namespace Modules\Pricing\Infrastructure\Providers;

use Illuminate\Support\Facades\Gate;
use Modules\Pricing\Domain\Contracts\ProductPriceRepositoryInterface;
use Modules\Pricing\Domain\Models\ProductPrice;
use Modules\Pricing\Infrastructure\Persistence\Repositories\EloquentProductPriceRepository;
use Modules\Pricing\Presentation\Filament\Policies\PricingManagementPolicy;
use Modules\Pricing\Presentation\Filament\RelationManagers\ProductPriceRelationManager;
use Modules\Shared\Infrastructure\Providers\ModuleServiceProvider;
use Modules\Shared\Presentation\Filament\ProductRelationRegistrar;

final class PricingServiceProvider extends ModuleServiceProvider
{
    protected function modulePath(): string
    {
        return dirname(__DIR__, 2);
    }

    public function register(): void
    {
        $this->app->bind(ProductPriceRepositoryInterface::class, EloquentProductPriceRepository::class);
    }

    public function boot(): void
    {
        parent::boot();

        Gate::policy(ProductPrice::class, PricingManagementPolicy::class);

        $this->app->make(ProductRelationRegistrar::class)
            ->register(ProductPriceRelationManager::class);
    }
}
