<?php

declare(strict_types=1);

namespace Modules\Pricing\Infrastructure\Providers;

use Filament\Panel;
use Illuminate\Support\Facades\Gate;
use Modules\Pricing\Domain\Contracts\ProductPriceRepositoryInterface;
use Modules\Pricing\Domain\Models\ProductPrice;
use Modules\Pricing\Infrastructure\Persistence\Repositories\EloquentProductPriceRepository;
use Modules\Pricing\Presentation\Filament\Policies\PricingManagementPolicy;
use Modules\Pricing\Presentation\Filament\Resource\ProductPriceResource;
use Modules\Shared\Infrastructure\Providers\ModuleServiceProvider;
use Modules\Shared\Presentation\Filament\Enums\PanelName;

final class PricingServiceProvider extends ModuleServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ProductPriceRepositoryInterface::class, EloquentProductPriceRepository::class);

        Panel::configureUsing(static function (Panel $panel): void {
            if ($panel->getId() !== PanelName::Admin->value) {
                return;
            }

            $panel->resources([
                ProductPriceResource::class,
            ]);
        });
    }

    public function boot(): void
    {
        parent::boot();

        Gate::policy(ProductPrice::class, PricingManagementPolicy::class);
    }
}
