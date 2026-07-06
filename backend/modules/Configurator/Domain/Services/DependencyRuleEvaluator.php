<?php

declare(strict_types=1);

namespace Modules\Configurator\Domain\Services;

use Illuminate\Support\Collection;
use Modules\Configurator\Application\DTO\ConfigurationAttributeStateData;
use Modules\Configurator\Application\DTO\ConfigurationEvaluationData;
use Modules\Configurator\Application\DTO\ConfiguratorSchemaData;
use Modules\Configurator\Domain\Enums\DependencyAction;
use Modules\Configurator\Domain\Models\Dependency;
use Modules\Configurator\Domain\ValueObjects\ConfigurationSelection;

final readonly class DependencyRuleEvaluator
{
    public function __construct(
        private DependencyConditionMatcher $matcher,
    ) {}

    /**
     * @param  Collection<int, Dependency>  $dependencies
     */
    public function evaluate(
        ConfiguratorSchemaData $schema,
        ConfigurationSelection $selection,
        Collection $dependencies,
    ): ConfigurationEvaluationData {
        /** @var array<string, array{key: string, visible: bool, required: bool, disabled: bool}> $states */
        $states = [];

        foreach ($schema->allAttributes() as $attribute) {
            $states[$attribute->id] = [
                'key' => $attribute->key,
                'visible' => true,
                'required' => $attribute->isRequired,
                'disabled' => false,
            ];
        }

        foreach ($dependencies as $dependency) {
            if ($dependency->action !== DependencyAction::Show) {
                continue;
            }

            $targetId = $dependency->targetAttribute->public_id;

            if (isset($states[$targetId])) {
                $states[$targetId]['visible'] = false;
            }
        }

        foreach ($dependencies as $dependency) {
            $sourceId = $dependency->sourceAttribute->public_id;
            $targetId = $dependency->targetAttribute->public_id;

            if (! isset($states[$sourceId], $states[$targetId])) {
                continue;
            }

            if (! $this->matcher->matches(
                $selection->get($sourceId),
                $dependency->condition,
                $dependency->condition_value,
            )) {
                continue;
            }

            $this->applyAction($states[$targetId], $dependency->action);
        }

        $attributes = [];

        foreach ($states as $id => $state) {
            $attributes[$id] = new ConfigurationAttributeStateData(
                id: $id,
                key: $state['key'],
                visible: $state['visible'],
                required: $state['required'],
                disabled: $state['disabled'],
            );
        }

        return new ConfigurationEvaluationData(
            productId: $schema->productId,
            attributes: $attributes,
        );
    }

    /**
     * @param  array{key: string, visible: bool, required: bool, disabled: bool}  $state
     */
    private function applyAction(array &$state, DependencyAction $action): void
    {
        match ($action) {
            DependencyAction::Show => $state['visible'] = true,
            DependencyAction::Hide => $state['visible'] = false,
            DependencyAction::Require => $state['required'] = true,
            DependencyAction::Disable => $state['disabled'] = true,
        };
    }
}
