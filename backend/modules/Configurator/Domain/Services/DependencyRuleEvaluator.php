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
        /** @var array<string, array{visible: bool, required: bool, disabled: bool}> $states */
        $states = [];

        foreach ($schema->allAttributes() as $attribute) {
            $states[$attribute->key] = [
                'visible' => true,
                'required' => $attribute->isRequired,
                'disabled' => false,
            ];
        }

        foreach ($dependencies as $dependency) {
            if ($dependency->action !== DependencyAction::Show) {
                continue;
            }

            $targetKey = $dependency->targetAttribute->key;

            if (isset($states[$targetKey])) {
                $states[$targetKey]['visible'] = false;
            }
        }

        foreach ($dependencies as $dependency) {
            $sourceKey = $dependency->sourceAttribute->key;
            $targetKey = $dependency->targetAttribute->key;

            if (! isset($states[$sourceKey], $states[$targetKey])) {
                continue;
            }

            if (! $this->matcher->matches(
                $selection->get($sourceKey),
                $dependency->condition,
                $dependency->condition_value,
            )) {
                continue;
            }

            $this->applyAction($states[$targetKey], $dependency->action);
        }

        $attributes = [];

        foreach ($states as $key => $state) {
            $attributes[$key] = new ConfigurationAttributeStateData(
                key: $key,
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
     * @param  array{visible: bool, required: bool, disabled: bool}  $state
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
