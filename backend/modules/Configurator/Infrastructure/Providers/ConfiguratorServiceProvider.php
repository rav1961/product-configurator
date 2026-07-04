<?php

declare(strict_types=1);

namespace Modules\Configurator\Infrastructure\Providers;

use Filament\Panel;
use Illuminate\Support\Facades\Gate;
use Modules\Configurator\Domain\Contracts\AttributeCollectionRepositoryInterface;
use Modules\Configurator\Domain\Contracts\AttributeRepositoryInterface;
use Modules\Configurator\Domain\Contracts\AttributeValueRepositoryInterface;
use Modules\Configurator\Domain\Contracts\DependencyRepositoryInterface;
use Modules\Configurator\Domain\Contracts\StepRepositoryInterface;
use Modules\Configurator\Domain\Models\Attribute;
use Modules\Configurator\Domain\Models\AttributeCollection;
use Modules\Configurator\Domain\Models\AttributeValue;
use Modules\Configurator\Domain\Models\Dependency;
use Modules\Configurator\Domain\Models\Step;
use Modules\Configurator\Infrastructure\Persistence\Repositories\EloquentAttributeCollectionRepository;
use Modules\Configurator\Infrastructure\Persistence\Repositories\EloquentAttributeRepository;
use Modules\Configurator\Infrastructure\Persistence\Repositories\EloquentAttributeValueRepository;
use Modules\Configurator\Infrastructure\Persistence\Repositories\EloquentDependencyRepository;
use Modules\Configurator\Infrastructure\Persistence\Repositories\EloquentStepRepository;
use Modules\Configurator\Presentation\Filament\Policies\ConfiguratorManagementPolicy;
use Modules\Configurator\Presentation\Filament\RelationManagers\AttributeCollectionsRelationManager;
use Modules\Configurator\Presentation\Filament\RelationManagers\DependenciesRelationManager;
use Modules\Configurator\Presentation\Filament\RelationManagers\StepsRelationManager;
use Modules\Configurator\Presentation\Filament\Resources\AttributeCollectionResource;
use Modules\Configurator\Presentation\Filament\Resources\AttributeResource;
use Modules\Configurator\Presentation\Filament\Resources\StepResource;
use Modules\Shared\Infrastructure\Providers\ModuleServiceProvider;
use Modules\Shared\Presentation\Filament\ProductRelationRegistrar;

final class ConfiguratorServiceProvider extends ModuleServiceProvider
{
    protected function modulePath(): string
    {
        return dirname(__DIR__, 2);
    }

    public function register(): void
    {
        $this->app->singleton(ProductRelationRegistrar::class);

        $this->app->bind(StepRepositoryInterface::class, EloquentStepRepository::class);
        $this->app->bind(AttributeRepositoryInterface::class, EloquentAttributeRepository::class);
        $this->app->bind(AttributeCollectionRepositoryInterface::class, EloquentAttributeCollectionRepository::class);
        $this->app->bind(AttributeValueRepositoryInterface::class, EloquentAttributeValueRepository::class);
        $this->app->bind(DependencyRepositoryInterface::class, EloquentDependencyRepository::class);

        Panel::configureUsing(static function (Panel $panel): void {
            if ($panel->getId() !== 'admin') {
                return;
            }

            $panel->resources([
                StepResource::class,
                AttributeCollectionResource::class,
                AttributeResource::class,
            ]);
        });
    }

    public function boot(): void
    {
        parent::boot();

        Gate::policy(Step::class, ConfiguratorManagementPolicy::class);
        Gate::policy(Attribute::class, ConfiguratorManagementPolicy::class);
        Gate::policy(AttributeCollection::class, ConfiguratorManagementPolicy::class);
        Gate::policy(AttributeValue::class, ConfiguratorManagementPolicy::class);
        Gate::policy(Dependency::class, ConfiguratorManagementPolicy::class);

        $registrar = $this->app->make(ProductRelationRegistrar::class);

        $registrar->register(StepsRelationManager::class);
        $registrar->register(AttributeCollectionsRelationManager::class);
        $registrar->register(DependenciesRelationManager::class);
    }
}
