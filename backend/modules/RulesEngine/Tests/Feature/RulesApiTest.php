<?php

declare(strict_types=1);

namespace Modules\RulesEngine\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Testing\TestResponse;
use Modules\Catalog\Domain\Enums\ProductStatus;
use Modules\Catalog\Domain\Models\Product;
use Modules\RulesEngine\Domain\Enums\RuleActionType;
use Modules\RulesEngine\Domain\Models\Rule;
use Modules\RulesEngine\Domain\Models\RuleAction;
use Modules\RulesEngine\Domain\Models\RuleCondition;
use Modules\RulesEngine\Domain\Models\RuleGroup;
use Modules\RulesEngine\Tests\Concerns\BuildsRulesFixtures;
use Modules\Shared\Domain\Enums\SelectionCondition;
use Modules\Users\Domain\Models\User;
use Tests\TestCase;

final class RulesApiTest extends TestCase
{
    use BuildsRulesFixtures;
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_guest_cannot_access_rules_evaluate(): void
    {
        $product = $this->ruleProduct();

        $this->rulesRequest($product->public_id)
            ->assertUnauthorized();
    }

    public function test_unverified_user_is_forbidden(): void
    {
        $product = $this->ruleProduct();

        $this->actingAs(User::factory()->unverified()->create())
            ->rulesRequest($product->public_id)
            ->assertForbidden();
    }

    public function test_unknown_product_returns_not_found(): void
    {
        $this->actingAs($this->user)
            ->rulesRequest('01JAAAAAAAAAAAAAAAAAAAAAAAAA')
            ->assertNotFound();
    }

    public function test_inactive_configurable_product_returns_not_found(): void
    {
        $product = Product::factory()->configurable()->create([
            'status' => ProductStatus::Draft,
        ]);

        $this->actingAs($this->user)
            ->rulesRequest($product->public_id)
            ->assertNotFound();
    }

    public function test_non_configurable_product_returns_422(): void
    {
        $product = Product::factory()->active()->create(['is_configurable' => false]);

        $this->actingAs($this->user)
            ->rulesRequest($product->public_id)
            ->assertUnprocessable();
    }

    public function test_evaluate_returns_matched_rules_and_effects(): void
    {
        ['product' => $product, 'attribute' => $color] = $this->productWithAttribute();
        $rule = Rule::factory()->for($product)->create(['name' => 'Szkło premium']);
        $group = RuleGroup::factory()->for($rule)->create();

        RuleCondition::factory()->for($group)->create([
            'source_attribute_id' => $color->id,
            'condition' => SelectionCondition::Equals,
            'condition_value' => 'red',
        ]);
        RuleAction::factory()->for($rule)->create([
            'type' => RuleActionType::AddModifier,
            'payload' => ['amount' => '199.99', 'label' => 'Szkło'],
            'position' => 0,
        ]);

        $this->actingAs($this->user)
            ->rulesRequest($product->public_id, [
                'selection' => [$color->public_id => 'red'],
            ])
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'productId',
                    'matchedRules' => [
                        ['id', 'name', 'position'],
                    ],
                    'effects' => [
                        'modifiers' => [
                            ['ruleId', 'amount', 'label', 'position'],
                        ],
                        'overrides',
                        'excludedOptions',
                        'messages',
                    ],
                ],
            ])
            ->assertJsonPath('data.productId', $product->public_id)
            ->assertJsonPath('data.matchedRules.0.name', 'Szkło premium')
            ->assertJsonPath('data.effects.modifiers.0.amount', '199.99')
            ->assertJsonPath('data.effects.modifiers.0.label', 'Szkło');
    }

    public function test_evaluate_returns_empty_when_no_rules_match(): void
    {
        ['product' => $product, 'attribute' => $color] = $this->productWithAttribute();
        $rule = Rule::factory()->for($product)->create();
        $group = RuleGroup::factory()->for($rule)->create();

        RuleCondition::factory()->for($group)->create([
            'source_attribute_id' => $color->id,
            'condition' => SelectionCondition::Equals,
            'condition_value' => 'red',
        ]);

        $this->actingAs($this->user)
            ->rulesRequest($product->public_id, [
                'selection' => [$color->public_id => 'blue'],
            ])
            ->assertOk()
            ->assertJsonPath('data.matchedRules', [])
            ->assertJsonPath('data.effects.modifiers', [])
            ->assertJsonPath('data.effects.overrides', [])
            ->assertJsonPath('data.effects.excludedOptions', [])
            ->assertJsonPath('data.effects.messages', []);
    }

    public function test_evaluate_requires_selection_array(): void
    {
        $product = $this->ruleProduct();

        $this->actingAs($this->user)
            ->rulesRequest($product->public_id, [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['selection']);
    }

    public function test_inactive_rules_are_excluded(): void
    {
        ['product' => $product, 'attribute' => $color] = $this->productWithAttribute();
        $rule = Rule::factory()->for($product)->inactive()->create();
        $group = RuleGroup::factory()->for($rule)->create();

        RuleCondition::factory()->for($group)->create([
            'source_attribute_id' => $color->id,
            'condition' => SelectionCondition::Equals,
            'condition_value' => 'red',
        ]);
        RuleAction::factory()->for($rule)->create([
            'type' => RuleActionType::AddModifier,
            'payload' => ['amount' => '50.00'],
        ]);

        $this->actingAs($this->user)
            ->rulesRequest($product->public_id, [
                'selection' => [$color->public_id => 'red'],
            ])
            ->assertOk()
            ->assertJsonPath('data.matchedRules', []);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return TestResponse<Response>
     */
    private function rulesRequest(string $productPublicId, array $payload = ['selection' => []]): TestResponse
    {
        return $this->postJson(
            route('api.products.rules.evaluate', ['productId' => $productPublicId]),
            $payload,
        );
    }
}
