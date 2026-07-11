<?php

declare(strict_types=1);

namespace Modules\RulesEngine\Tests\Unit;

use Modules\RulesEngine\Domain\Enums\MatchMode;
use Tests\TestCase;

final class MatchModeTest extends TestCase
{
    public function test_label_resolves_polish_translation(): void
    {
        $this->assertSame('Wszystkie (AND)', MatchMode::All->label());
        $this->assertSame('Dowolny (OR)', MatchMode::Any->label());
    }
}
