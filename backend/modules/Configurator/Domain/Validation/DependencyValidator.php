<?php

declare(strict_types=1);

namespace Modules\Configurator\Domain\Validation;

use Modules\Configurator\Domain\Exceptions\InvalidDependencyScopeException;
use Modules\Configurator\Domain\Models\Dependency;

final readonly class DependencyValidator
{
    public function validate(Dependency $dependency): void
    {
        if ($dependency->condition->requiredValue() && blank($dependency->condition_value)) {
            throw InvalidDependencyScopeException::conditionValueRequired();
        }

        if (! $dependency->isDirty(['product_id', 'source_attribute_id', 'target_attribute_id'])) {
            return;
        }

        $source = $dependency->sourceAttribute()
            ->with('step')
            ->first();

        $target = $dependency->targetAttribute()
            ->with('step')
            ->first();

        if ($source === null || $target === null) {
            return;
        }

        $sourceProductId = $source->step->product_id;
        $targetProductId = $target->step->product_id;

        if ($sourceProductId !== $targetProductId
            || $sourceProductId !== $dependency->product_id
        ) {
            throw InvalidDependencyScopeException::attributesMustBelongsToProduct();
        }
    }
}
