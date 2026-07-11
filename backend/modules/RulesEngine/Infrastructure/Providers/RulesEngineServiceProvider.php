<?php

declare(strict_types=1);

namespace Modules\RulesEngine\Infrastructure\Providers;

use Modules\RulesEngine\Domain\Contracts\RuleActionRepositoryInterface;
use Modules\RulesEngine\Domain\Contracts\RuleConditionRepositoryInterface;
use Modules\RulesEngine\Domain\Contracts\RuleGraphRepositoryInterface;
use Modules\RulesEngine\Domain\Contracts\RuleGroupRepositoryInterface;
use Modules\RulesEngine\Domain\Contracts\RuleRepositoryInterface;
use Modules\RulesEngine\Infrastructure\Persistence\Repositories\EloquentRuleActionRepository;
use Modules\RulesEngine\Infrastructure\Persistence\Repositories\EloquentRuleConditionRepository;
use Modules\RulesEngine\Infrastructure\Persistence\Repositories\EloquentRuleGraphRepository;
use Modules\RulesEngine\Infrastructure\Persistence\Repositories\EloquentRuleGroupRepository;
use Modules\RulesEngine\Infrastructure\Persistence\Repositories\EloquentRuleRepository;
use Modules\Shared\Infrastructure\Providers\ModuleServiceProvider;

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
    }
}
