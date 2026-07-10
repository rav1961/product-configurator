<?php

declare(strict_types=1);

namespace Modules\Shared\Tests\Unit;

use Modules\Shared\Domain\Enums\SelectionCondition;
use Tests\TestCase;

final class SelectionConditionTest extends TestCase
{
    public function test_required_value_only_for_equals_and_not_equals(): void
    {
        $this->assertTrue(SelectionCondition::Equals->requiredValue());
        $this->assertTrue(SelectionCondition::NotEquals->requiredValue());
        $this->assertFalse(SelectionCondition::IsSet->requiredValue());
        $this->assertFalse(SelectionCondition::IsEmpty->requiredValue());
        $this->assertFalse(SelectionCondition::IsNotSet->requiredValue());
    }

    public function test_label_resolves_polish_translation(): void
    {
        $this->assertSame('Równa się', SelectionCondition::Equals->label());
        $this->assertSame('Nie równa się', SelectionCondition::NotEquals->label());
        $this->assertSame('Nie jest ustawione', SelectionCondition::IsNotSet->label());
    }
}
