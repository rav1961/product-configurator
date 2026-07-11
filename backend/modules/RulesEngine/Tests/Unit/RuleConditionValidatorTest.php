<?php

declare(strict_types=1);

namespace Modules\RulesEngine\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Catalog\Domain\Models\Product;
use Modules\Configurator\Domain\Models\Attribute;
use Modules\Configurator\Domain\Models\Step;
use Modules\RulesEngine\Domain\Exceptions\InvalidRuleScopeException;
use Modules\RulesEngine\Domain\Models\Rule;
use Modules\RulesEngine\Domain\Models\RuleCondition;
use Modules\RulesEngine\Domain\Models\RuleGroup;
use Modules\RulesEngine\Domain\Validation\RuleConditionValidator;
use Modules\Shared\Domain\Enums\SelectionCondition;
use Tests\TestCase;

final class RuleConditionValidatorTest extends TestCase
{
    use RefreshDatabase;

    private RuleConditionValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->validator = new RuleConditionValidator;
    }

    public function test_rejects_equals_without_condition_value(): void
    {
        $condition = new RuleCondition([
            'condition' => SelectionCondition::Equals,
            'condition_value' => null,
        ]);

        $this->expectException(InvalidRuleScopeException::class);

        $this->validator->validate($condition);
    }

    public function test_rejects_foreign_source_attribute(): void
    {
        $product = Product::factory()->create();
        $foreignProduct = Product::factory()->create();

        $rule = Rule::factory()->for($product)->create();
        $group = RuleGroup::factory()->for($rule)->create();

        $foreignStep = Step::factory()->create([
            'product_id' => $foreignProduct->id,
        ]);
        $foreignAttribute = Attribute::factory()->for($foreignStep)->create();

        $condition = new RuleCondition([
            'rule_group_id' => $group->id,
            'source_attribute_id' => $foreignAttribute->id,
            'condition' => SelectionCondition::IsSet,
            'condition_value' => null,
        ]);

        $this->expectException(InvalidRuleScopeException::class);

        $this->validator->validate($condition);
    }

    public function test_accepts_attribute_from_same_product(): void
    {
        $product = Product::factory()->create();
        $rule = Rule::factory()->for($product)->create();
        $group = RuleGroup::factory()->for($rule)->create();
        $step = Step::factory()->create(['product_id' => $product->id]);
        $attribute = Attribute::factory()->for($step)->create();

        $condition = new RuleCondition([
            'rule_group_id' => $group->id,
            'source_attribute_id' => $attribute->id,
            'condition' => SelectionCondition::IsSet,
            'condition_value' => null,
        ]);

        $this->validator->validate($condition);

        $this->addToAssertionCount(1);
    }
}
