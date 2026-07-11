<?php

declare(strict_types=1);

namespace Modules\RulesEngine\Tests\Unit;

use Modules\RulesEngine\Domain\Enums\RuleActionType;
use Modules\RulesEngine\Domain\Exceptions\InvalidRuleScopeException;
use Modules\RulesEngine\Domain\Validation\RuleActionPayloadValidator;
use Tests\TestCase;

final class RuleActionPayloadValidatorTest extends TestCase
{
    private RuleActionPayloadValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new RuleActionPayloadValidator;
    }

    public function test_accepts_valid_add_modifier_payload(): void
    {
        $this->validator->validate(RuleActionType::AddModifier, [
            'amount' => '199.99',
            'label' => 'Glass',
        ]);

        $this->addToAssertionCount(1);
    }

    public function test_rejects_add_modifier_without_amount(): void
    {
        $this->expectException(InvalidRuleScopeException::class);

        $this->validator->validate(RuleActionType::AddModifier, [
            'label' => 'Glass',
        ]);
    }

    public function test_accepts_valid_set_override_payload(): void
    {
        $this->validator->validate(RuleActionType::SetOverride, [
            'amount' => '2499.00',
        ]);
        $this->addToAssertionCount(1);
    }

    public function test_accepts_valid_exclude_option_payload(): void
    {
        $this->validator->validate(RuleActionType::ExcludeOption, [
            'attribute_id' => '01JABCDEFGHJKMNPQRSTVWXYZ0',
            'value' => 'glass',
        ]);
        $this->addToAssertionCount(1);
    }

    public function test_rejects_exclude_option_without_value(): void
    {
        $this->expectException(InvalidRuleScopeException::class);
        $this->validator->validate(RuleActionType::ExcludeOption, [
            'attribute_id' => '01JABCDEFGHJKMNPQRSTVWXYZ0',
        ]);
    }

    public function test_accepts_valid_add_message_payload(): void
    {
        $this->validator->validate(RuleActionType::AddMessage, [
            'level' => 'warning',
            'message' => 'lorem ipsum',
        ]);
        $this->addToAssertionCount(1);
    }

    public function test_rejects_add_message_with_invalid_level(): void
    {
        $this->expectException(InvalidRuleScopeException::class);
        $this->validator->validate(RuleActionType::AddMessage, [
            'level' => 'critical',
            'message' => 'Test',
        ]);
    }
}
