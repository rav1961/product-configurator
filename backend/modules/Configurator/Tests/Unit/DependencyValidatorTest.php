<?php

declare(strict_types=1);

namespace Modules\Configurator\Tests\Unit;

use Modules\Configurator\Domain\Exceptions\InvalidDependencyScopeException;
use Modules\Configurator\Domain\Models\Dependency;
use Modules\Configurator\Domain\Validation\DependencyValidator;
use Modules\Shared\Domain\Enums\SelectionCondition;
use Tests\TestCase;

final class DependencyValidatorTest extends TestCase
{
    private DependencyValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->validator = new DependencyValidator;
    }

    public function test_rejects_equals_without_condition_value(): void
    {
        $dependency = new Dependency([
            'condition' => SelectionCondition::Equals,
            'condition_value' => null,
        ]);

        $this->expectException(InvalidDependencyScopeException::class);

        $this->validator->validate($dependency);
    }

    public function test_accepts_is_set_without_condition_value_when_scope_unchanged(): void
    {
        $dependency = new Dependency([
            'product_id' => 1,
            'source_attribute_id' => 10,
            'target_attribute_id' => 20,
            'condition' => SelectionCondition::IsSet,
            'condition_value' => null,
        ]);

        $dependency->syncOriginal();

        $this->validator->validate($dependency);

        $this->assertSame(SelectionCondition::IsSet, $dependency->condition);
    }

    public function test_rejects_not_equals_without_condition_value(): void
    {
        $dependency = new Dependency([
            'condition' => SelectionCondition::NotEquals,
            'condition_value' => null,
        ]);

        $this->expectException(InvalidDependencyScopeException::class);

        $this->validator->validate($dependency);
    }

    public function test_accepts_is_not_set_without_condition_value_when_scope_unchanged(): void
    {
        $dependency = new Dependency([
            'product_id' => 1,
            'source_attribute_id' => 10,
            'target_attribute_id' => 20,
            'condition' => SelectionCondition::IsNotSet,
            'condition_value' => null,
        ]);
        $dependency->syncOriginal();

        $this->validator->validate($dependency);

        $this->assertSame(SelectionCondition::IsNotSet, $dependency->condition);
    }
}
