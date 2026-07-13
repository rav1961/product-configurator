<?php

declare(strict_types=1);

namespace Modules\SavedConfiguration\Infrastructure\Providers;

use Modules\SavedConfiguration\Domain\Contracts\SavedConfigurationRepositoryInterface;
use Modules\SavedConfiguration\Infrastructure\Persistence\Repositories\EloquentSavedConfigurationRepository;
use Modules\Shared\Infrastructure\Providers\ModuleServiceProvider;

final class SavedConfigurationServiceProvider extends ModuleServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            SavedConfigurationRepositoryInterface::class,
            EloquentSavedConfigurationRepository::class,
        );
    }
}
