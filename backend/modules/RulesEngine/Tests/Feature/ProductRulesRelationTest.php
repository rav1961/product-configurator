<?php

declare(strict_types=1);

namespace Modules\RulesEngine\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Catalog\Domain\Models\Product;
use Modules\Configurator\Domain\Models\Attribute;
use Modules\Configurator\Domain\Models\Step;
use Modules\RulesEngine\Domain\Exceptions\InvalidRuleScopeException;
use Modules\RulesEngine\Domain\Models\Rule;
use Modules\RulesEngine\Domain\Models\RuleCondition;
use Modules\RulesEngine\Domain\Models\RuleGroup;
use Modules\Shared\Domain\Enums\SelectionCondition;
use Tests\TestCase;

final class ProductRulesRelationTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_exposes_rules_relation(): void
    {
        $product = Product::factory()->create();
        $rule = Rule::factory()->for($product)->create();

        $product->load('rules');

        $this->assertTrue($product->rules->contains($rule));
    }

    public function test_deleting_product_cascades_rules_graph(): void
    {
        $product = Product::factory()->create();
        $rule = Rule::factory()->for($product)->create();
        $group = RuleGroup::factory()->for($rule)->create();
        $condition = RuleCondition::factory()->for($group)->create();

        $product->delete();

        $this->assertDatabaseMissing('rules_engine_rules', ['id' => $rule->id]);
        $this->assertDatabaseMissing('rules_engine_rule_groups', ['id' => $group->id]);
        $this->assertDatabaseMissing('rules_engine_rule_conditions', ['id' => $condition->id]);
    }

    public function test_observer_rejects_foreign_source_attribute_on_create(): void
    {
        $product = Product::factory()->create();
        $foreignProduct = Product::factory()->create();
        $rule = Rule::factory()->for($product)->create();
        $group = RuleGroup::factory()->for($rule)->create();
        $foreignStep = Step::factory()->create(['product_id' => $foreignProduct->id]);
        $foreignAttribute = Attribute::factory()->for($foreignStep)->create();

        $this->expectException(InvalidRuleScopeException::class);

        RuleCondition::query()->create([
            'public_id' => (string) Str::ulid(),
            'rule_group_id' => $group->id,
            'source_attribute_id' => $foreignAttribute->id,
            'condition' => SelectionCondition::Equals,
            'condition_value' => 'glass',
            'position' => 0,
        ]);
    }
}
