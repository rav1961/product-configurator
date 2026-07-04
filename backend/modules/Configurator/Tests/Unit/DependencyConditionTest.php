<?php

declare(strict_types=1);

namespace Modules\Configurator\Tests\Unit;

use Modules\Configurator\Domain\Enums\DependencyCondition;
use Tests\TestCase;

final class DependencyConditionTest extends TestCase
{
    public function test_required_value_only_for_equals_and_not_equals(): void
    {
        $this->assertTrue(DependencyCondition::Equals->requiredValue());
        $this->assertTrue(DependencyCondition::NotEquals->requiredValue());
        $this->assertFalse(DependencyCondition::IsSet->requiredValue());
        $this->assertFalse(DependencyCondition::IsEmpty->requiredValue());
        $this->assertFalse(DependencyCondition::IsNotSet->requiredValue());
    }

    public function test_label_resolves_polish_translation(): void
    {
        $this->assertSame('Równa się', DependencyCondition::Equals->label());
        $this->assertSame('Nie równa się', DependencyCondition::NotEquals->label());
        $this->assertSame('Nie jest ustawione', DependencyCondition::IsNotSet->label());
    }
}
