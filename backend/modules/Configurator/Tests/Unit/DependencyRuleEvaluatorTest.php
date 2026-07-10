<?php

declare(strict_types=1);

namespace Modules\Configurator\Tests\Unit;

use Modules\Configurator\Domain\Enums\DependencyAction;
use Modules\Configurator\Domain\Services\DependencyRuleEvaluator;
use Modules\Configurator\Domain\ValueObjects\ConfigurationSelection;
use Modules\Configurator\Tests\Concerns\BuildsConfiguratorSchema;
use Modules\Shared\Domain\Enums\SelectionCondition;
use Modules\Shared\Domain\Services\SelectionConditionMatcher;
use Tests\TestCase;

final class DependencyRuleEvaluatorTest extends TestCase
{
    use BuildsConfiguratorSchema;

    private DependencyRuleEvaluator $evaluator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->evaluator = new DependencyRuleEvaluator(new SelectionConditionMatcher);
    }

    public function test_show_target_is_hidden_until_condition_matches(): void
    {
        $color = $this->schemaAttribute('color');
        $finish = $this->schemaAttribute('finish');
        $schema = $this->schemaWithAttributes($color, $finish);
        $dependencies = collect([
            $this->schemaDependency(
                source: $color,
                target: $finish,
                condition: SelectionCondition::Equals,
                conditionValue: 'red',
                action: DependencyAction::Show,
            ),
        ]);

        $hidden = $this->evaluator->evaluate(
            $schema,
            ConfigurationSelection::fromArray([$color->id => 'blue']),
            $dependencies,
        );
        $this->assertFalse($hidden->attributes[$finish->id]->visible);

        $visible = $this->evaluator->evaluate(
            $schema,
            ConfigurationSelection::fromArray([$color->id => 'red']),
            $dependencies,
        );
        $this->assertTrue($visible->attributes[$finish->id]->visible);
    }

    public function test_later_dependency_overrides_earlier_for_same_target(): void
    {
        $color = $this->schemaAttribute('color');
        $finish = $this->schemaAttribute('finish');
        $schema = $this->schemaWithAttributes($color, $finish);
        $dependencies = collect([
            $this->schemaDependency(
                source: $color,
                target: $finish,
                condition: SelectionCondition::Equals,
                conditionValue: 'red',
                action: DependencyAction::Show,
                position: 0,
            ),
            $this->schemaDependency(
                source: $color,
                target: $finish,
                condition: SelectionCondition::Equals,
                conditionValue: 'red',
                action: DependencyAction::Hide,
                position: 1,
            ),
        ]);

        $evaluation = $this->evaluator->evaluate(
            $schema,
            ConfigurationSelection::fromArray([$color->id => 'red']),
            $dependencies,
        );

        $this->assertFalse($evaluation->attributes[$finish->id]->visible);
    }
}
