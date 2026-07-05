<?php

declare(strict_types=1);

namespace Modules\Configurator\Tests\Unit;

use Modules\Configurator\Application\DTO\ConfigurationAttributeData;
use Modules\Configurator\Application\DTO\ConfigurationStepData;
use Modules\Configurator\Application\DTO\ConfiguratorSchemaData;
use Modules\Configurator\Domain\Enums\DependencyAction;
use Modules\Configurator\Domain\Enums\DependencyCondition;
use Modules\Configurator\Domain\Models\Attribute;
use Modules\Configurator\Domain\Models\Dependency;
use Modules\Configurator\Domain\Services\DependencyConditionMatcher;
use Modules\Configurator\Domain\Services\DependencyRuleEvaluator;
use Modules\Configurator\Domain\ValueObjects\ConfigurationSelection;
use Tests\TestCase;

final class DependencyRuleEvaluatorTest extends TestCase
{
    private DependencyRuleEvaluator $evaluator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->evaluator = new DependencyRuleEvaluator(new DependencyConditionMatcher);
    }

    public function test_show_target_is_hidden_until_condition_matches(): void
    {
        $schema = $this->schemaWithAttributes(
            $this->attribute('color', isRequired: false),
            $this->attribute('finish', isRequired: false),
        );
        $dependencies = collect([
            $this->dependency(
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

    public function test_hide_target_when_condition_matches(): void
    {
        $schema = $this->schemaWithAttributes(
            $this->attribute('color'),
            $this->attribute('finish'),
        );

        $dependencies = collect([
            $this->dependency(
                sourceKey: 'color',
                targetKey: 'finish',
                condition: DependencyCondition::Equals,
                conditionValue: 'red',
                action: DependencyAction::Hide,
            ),
        ]);

        $evaluation = $this->evaluator->evaluate(
            $schema,
            ConfigurationSelection::fromArray(['color' => 'red']),
            $dependencies,
        );
        $this->assertFalse($evaluation->attributes['finish']->visible);

        $visibleWhenBlue = $this->evaluator->evaluate(
            $schema,
            ConfigurationSelection::fromArray(['color' => 'blue']),
            $dependencies,
        );

        $this->assertTrue($visibleWhenBlue->attributes['finish']->visible);
    }

    public function test_require_and_disable_apply_only_when_condition_matches(): void
    {
        $schema = $this->schemaWithAttributes(
            $this->attribute('color'),
            $this->attribute('finish', isRequired: false),
        );
        $dependencies = collect([
            $this->dependency(
                sourceKey: 'color',
                targetKey: 'finish',
                condition: DependencyCondition::Equals,
                conditionValue: 'red',
                action: DependencyAction::Require,
                position: 0,
            ),
            $this->dependency(
                sourceKey: 'color',
                targetKey: 'finish',
                condition: DependencyCondition::Equals,
                conditionValue: 'red',
                action: DependencyAction::Disable,
                position: 1,
            ),
        ]);

        $matched = $this->evaluator->evaluate(
            $schema,
            ConfigurationSelection::fromArray(['color' => 'red']),
            $dependencies,
        );

        $this->assertTrue($matched->attributes['finish']->required);
        $this->assertTrue($matched->attributes['finish']->disabled);
        $unmatched = $this->evaluator->evaluate(
            $schema,
            ConfigurationSelection::fromArray(['color' => 'blue']),
            $dependencies,
        );
        $this->assertFalse($unmatched->attributes['finish']->required);
        $this->assertFalse($unmatched->attributes['finish']->disabled);
    }

    public function test_later_dependency_overrides_earlier_for_same_target(): void
    {
        $schema = $this->schemaWithAttributes(
            $this->attribute('color'),
            $this->attribute('finish'),
        );
        $dependencies = collect([
            $this->dependency(
                sourceKey: 'color',
                targetKey: 'finish',
                condition: DependencyCondition::Equals,
                conditionValue: 'red',
                action: DependencyAction::Show,
                position: 0,
            ),
            $this->dependency(
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

    private function schemaWithAttributes(ConfigurationAttributeData ...$attributes): ConfiguratorSchemaData
    {
        return new ConfiguratorSchemaData(
            productId: 'product-public-id',
            productName: 'Product',
            steps: [
                new ConfigurationStepData(
                    id: 'step-public-id',
                    name: 'Step',
                    position: 0,
                    attributes: array_values($attributes),
                ),
            ],
        );
    }

    private function attribute(string $key, bool $isRequired = false): ConfigurationAttributeData
    {
        return new ConfigurationAttributeData(
            id: "{$key}-public-id",
            key: $key,
            name: ucfirst($key),
            type: 'text',
            position: 0,
            isRequired: $isRequired,
            options: [],
        );
    }

    private function dependency(
        string $sourceKey,
        string $targetKey,
        DependencyCondition $condition,
        ?string $conditionValue,
        DependencyAction $action,
        int $position = 0,
    ): Dependency {
        $dependency = new Dependency([
            'condition' => $condition,
            'condition_value' => $conditionValue,
            'action' => $action,
            'position' => $position,
        ]);

        $dependency->setRelation('sourceAttribute', new Attribute(['key' => $sourceKey]));
        $dependency->setRelation('targetAttribute', new Attribute(['key' => $targetKey]));

        return $dependency;
    }
}
