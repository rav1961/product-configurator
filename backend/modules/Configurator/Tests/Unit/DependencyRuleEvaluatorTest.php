<?php

declare(strict_types=1);

namespace Modules\Configurator\Tests\Unit;

use Modules\Configurator\Domain\Enums\DependencyAction;
use Modules\Configurator\Domain\Enums\DependencyCondition;
use Modules\Configurator\Domain\Services\DependencyConditionMatcher;
use Modules\Configurator\Domain\Services\DependencyRuleEvaluator;
use Modules\Configurator\Domain\ValueObjects\ConfigurationSelection;
use Modules\Configurator\Tests\Concerns\BuildsConfiguratorSchema;
use Tests\TestCase;

final class DependencyRuleEvaluatorTest extends TestCase
{
    use BuildsConfiguratorSchema;

    private DependencyRuleEvaluator $evaluator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->evaluator = new DependencyRuleEvaluator(new DependencyConditionMatcher);
    }

    public function test_show_target_is_hidden_until_condition_matches(): void
    {
        $schema = $this->schemaWithAttributes(
            $this->schemaAttribute('color'),
            $this->schemaAttribute('finish'),
        );
        $dependencies = collect([
            $this->schemaDependency(
                sourceKey: 'color',
                targetKey: 'finish',
                condition: DependencyCondition::Equals,
                conditionValue: 'red',
                action: DependencyAction::Show,
            ),
        ]);

        $hidden = $this->evaluator->evaluate(
            $schema,
            ConfigurationSelection::fromArray(['color' => 'blue']),
            $dependencies,
        );
        $this->assertFalse($hidden->attributes['finish']->visible);

        $visible = $this->evaluator->evaluate(
            $schema,
            ConfigurationSelection::fromArray(['color' => 'red']),
            $dependencies,
        );
        $this->assertTrue($visible->attributes['finish']->visible);
    }

    public function test_later_dependency_overrides_earlier_for_same_target(): void
    {
        $schema = $this->schemaWithAttributes(
            $this->schemaAttribute('color'),
            $this->schemaAttribute('finish'),
        );
        $dependencies = collect([
            $this->schemaDependency(
                sourceKey: 'color',
                targetKey: 'finish',
                condition: DependencyCondition::Equals,
                conditionValue: 'red',
                action: DependencyAction::Show,
                position: 0,
            ),
            $this->schemaDependency(
                sourceKey: 'color',
                targetKey: 'finish',
                condition: DependencyCondition::Equals,
                conditionValue: 'red',
                action: DependencyAction::Hide,
                position: 1,
            ),
        ]);

        $evaluation = $this->evaluator->evaluate(
            $schema,
            ConfigurationSelection::fromArray(['color' => 'red']),
            $dependencies,
        );

        $this->assertFalse($evaluation->attributes['finish']->visible);
    }
}
