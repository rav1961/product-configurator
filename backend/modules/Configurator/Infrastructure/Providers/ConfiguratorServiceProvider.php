<?php

declare(strict_types=1);

namespace Modules\Configurator\Infrastructure\Providers;

use Modules\Configurator\Domain\Contracts\AttributeCollectionRepositoryInterface;
use Modules\Configurator\Domain\Contracts\AttributeRepositoryInterface;
use Modules\Configurator\Domain\Contracts\StepRepositoryInterface;
use Modules\Configurator\Infrastructure\Persistence\Repositories\EloquentAttributeCollectionRepository;
use Modules\Configurator\Infrastructure\Persistence\Repositories\EloquentAttributeRepository;
use Modules\Configurator\Infrastructure\Persistence\Repositories\EloquentStepRepository;
use Modules\Shared\Infrastructure\Providers\ModuleServiceProvider;

final class ConfiguratorServiceProvider extends ModuleServiceProvider
{
    protected function modulePath(): string
    {
        return dirname(__DIR__, 2);
    }

    public function register(): void
    {
        $this->app->bind(StepRepositoryInterface::class, EloquentStepRepository::class);
        $this->app->bind(AttributeRepositoryInterface::class, EloquentAttributeRepository::class);
        $this->app->bind(AttributeCollectionRepositoryInterface::class, EloquentAttributeCollectionRepository::class);
    }
}
