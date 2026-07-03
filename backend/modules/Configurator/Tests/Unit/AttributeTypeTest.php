<?php

declare(strict_types=1);

namespace Modules\Configurator\Tests\Unit;

use Modules\Configurator\Domain\Enums\AttributeType;
use Tests\TestCase;

final class AttributeTypeTest extends TestCase
{
    public function test_option_types_have_options(): void
    {
        $this->assertTrue(AttributeType::Select->hasOptions());
        $this->assertTrue(AttributeType::MultiSelect->hasOptions());
        $this->assertFalse(AttributeType::Text->hasOptions());
    }

    public function test_label_resolves_polish_translation(): void
    {
        $this->assertSame('Lista wyboru', AttributeType::Select->label());
        $this->assertSame('Tekst', AttributeType::Text->label());
    }
}
