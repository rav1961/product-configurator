<?php

declare(strict_types=1);

namespace Modules\RulesEngine\Tests\Unit;

use Modules\RulesEngine\Domain\Enums\RuleActionType;
use Tests\TestCase;

final class RuleActionTypeTest extends TestCase
{
    public function test_label_resolves_polish_translation(): void
    {
        $this->assertSame('Dopłata', RuleActionType::AddModifier->label());
        $this->assertSame('Nadpisanie ceny', RuleActionType::SetOverride->label());
        $this->assertSame('Wykluczenie opcji', RuleActionType::ExcludeOption->label());
        $this->assertSame('Komunikat', RuleActionType::AddMessage->label());
    }
}
