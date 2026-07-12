<?php

declare(strict_types=1);

namespace Modules\RulesEngine\Infrastructure\Providers;

use Filament\Panel;
use Illuminate\Support\Facades\Gate;
use Modules\RulesEngine\Domain\Contracts\RuleActionRepositoryInterface;
use Modules\RulesEngine\Domain\Contracts\RuleConditionRepositoryInterface;
use Modules\RulesEngine\Domain\Contracts\RuleGraphRepositoryInterface;
use Modules\RulesEngine\Domain\Contracts\RuleGroupRepositoryInterface;
use Modules\RulesEngine\Domain\Contracts\RuleRepositoryInterface;
use Modules\RulesEngine\Domain\Models\Rule;
use Modules\RulesEngine\Domain\Models\RuleAction;
use Modules\RulesEngine\Domain\Models\RuleCondition;
use Modules\RulesEngine\Domain\Models\RuleGroup;
use Modules\RulesEngine\Infrastructure\Persistence\Repositories\EloquentRuleActionRepository;
use Modules\RulesEngine\Infrastructure\Persistence\Repositories\EloquentRuleConditionRepository;
use Modules\RulesEngine\Infrastructure\Persistence\Repositories\EloquentRuleGraphRepository;
use Modules\RulesEngine\Infrastructure\Persistence\Repositories\EloquentRuleGroupRepository;
use Modules\RulesEngine\Infrastructure\Persistence\Repositories\EloquentRuleRepository;
use Modules\RulesEngine\Presentation\Filament\Policies\RuleManagementPolicy;
use Modules\RulesEngine\Presentation\Filament\RelationManagers\RulesRelationManager;
use Modules\RulesEngine\Presentation\Filament\Resources\RuleGroupResource;
use Modules\RulesEngine\Presentation\Filament\Resources\RuleResource;
use Modules\Shared\Infrastructure\Providers\ModuleServiceProvider;
use Modules\Shared\Presentation\Filament\ProductRelationRegistrar;

final class RulesEngineServiceProvider extends ModuleServiceProvider
{
    protected function modulePath(): string
    {
        return dirname(__DIR__, 2);
    }

    public function register(): void
    {
        $this->app->bind(RuleRepositoryInterface::class, EloquentRuleRepository::class);
        $this->app->bind(RuleGroupRepositoryInterface::class, EloquentRuleGroupRepository::class);
        $this->app->bind(RuleConditionRepositoryInterface::class, EloquentRuleConditionRepository::class);
        $this->app->bind(RuleActionRepositoryInterface::class, EloquentRuleActionRepository::class);
        $this->app->bind(RuleGraphRepositoryInterface::class, EloquentRuleGraphRepository::class);

        Panel::configureUsing(static function (Panel $panel) {
            if ($panel->getId() !== 'admin') {
                return;
            }

            $panel->resources([
                RuleResource::class,
                RuleGroupResource::class,
            ]);
        });
    }

    public function boot(): void
    {
        parent::boot();

        Gate::policy(Rule::class, RuleManagementPolicy::class);
        Gate::policy(RuleGroup::class, RuleManagementPolicy::class);
        Gate::policy(RuleCondition::class, RuleManagementPolicy::class);
        Gate::policy(RuleAction::class, RuleManagementPolicy::class);

        $this->app->make(ProductRelationRegistrar::class)
            ->register(RulesRelationManager::class);
    }
}
