<?php

declare(strict_types=1);

namespace Modules\Pricing\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Testing\TestResponse;
use Modules\Catalog\Domain\Enums\ProductStatus;
use Modules\Catalog\Domain\Models\Product;
use Modules\Pricing\Domain\Models\ProductPrice;
use Modules\RulesEngine\Domain\Enums\RuleActionType;
use Modules\RulesEngine\Domain\Models\Rule;
use Modules\RulesEngine\Domain\Models\RuleAction;
use Modules\RulesEngine\Domain\Models\RuleCondition;
use Modules\RulesEngine\Domain\Models\RuleGroup;
use Modules\RulesEngine\Tests\Concerns\BuildsRulesFixtures;
use Modules\Shared\Domain\Enums\SelectionCondition;
use Modules\Users\Domain\Models\User;
use Tests\TestCase;

final class PricingApiTest extends TestCase
{
    use BuildsRulesFixtures;
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_guest_cannot_calculate_price(): void
    {
        $product = $this->ruleProduct();

        $this->priceRequest($product->public_id)
            ->assertUnauthorized();
    }

    public function test_calculates_total_with_add_modifier(): void
    {
        ['product' => $product, 'attribute' => $color] = $this->productWithAttribute();
        ProductPrice::factory()->for($product)->create(['amount' => 199_900]);

        $rule = Rule::factory()->for($product)->create();
        $group = RuleGroup::factory()->for($rule)->create();
        RuleCondition::factory()->for($group)->create([
            'source_attribute_id' => $color->id,
            'condition' => SelectionCondition::Equals,
            'condition_value' => 'red',
        ]);
        RuleAction::factory()->for($rule)->create([
            'type' => RuleActionType::AddModifier,
            'payload' => ['amount' => 19_999, 'operation' => 'add'],
        ]);

        $this->actingAs($this->user)
            ->priceRequest($product->public_id, [
                'selection' => [$color->public_id => 'red'],
            ])
            ->assertOk()
            ->assertJsonPath('data.productId', $product->public_id)
            ->assertJsonPath('data.basePrice', 199_900)
            ->assertJsonPath('data.total', 219_899)
            ->assertJsonPath('data.hasOverride', false);
    }

    public function test_applies_override(): void
    {
        ['product' => $product, 'attribute' => $color] = $this->productWithAttribute();
        ProductPrice::factory()->for($product)->create(['amount' => 199_900]);

        $rule = Rule::factory()->for($product)->create();
        $group = RuleGroup::factory()->for($rule)->create();
        RuleCondition::factory()->for($group)->create([
            'source_attribute_id' => $color->id,
            'condition' => SelectionCondition::IsSet,
            'condition_value' => null,
        ]);
        RuleAction::factory()->for($rule)->create([
            'type' => RuleActionType::SetOverride,
            'payload' => ['amount' => 149_900],
        ]);

        $this->actingAs($this->user)
            ->priceRequest($product->public_id, [
                'selection' => [$color->public_id => 'red'],
            ])
            ->assertOk()
            ->assertJsonPath('data.total', 149_900)
            ->assertJsonPath('data.hasOverride', true);
    }

    public function test_returns_422_when_base_price_missing(): void
    {
        $product = $this->ruleProduct();

        $this->actingAs($this->user)
            ->priceRequest($product->public_id)
            ->assertUnprocessable();
    }

    public function test_non_configurable_product_returns_422(): void
    {
        $product = Product::factory()->active()->create(['is_configurable' => false]);
        ProductPrice::factory()->for($product)->create(['amount' => 99_900]);

        $this->actingAs($this->user)
            ->priceRequest($product->public_id)
            ->assertUnprocessable();
    }

    public function test_inactive_product_returns_not_found(): void
    {
        $product = Product::factory()->configurable()->create([
            'status' => ProductStatus::Draft,
        ]);
        ProductPrice::factory()->for($product)->create(['amount' => 99_900]);

        $this->actingAs($this->user)
            ->priceRequest($product->public_id)
            ->assertNotFound();
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return TestResponse<Response>
     */
    private function priceRequest(string $productPublicId, array $payload = ['selection' => []]): TestResponse
    {
        return $this->postJson(
            route('api.products.price.calculate', ['productId' => $productPublicId]),
            $payload,
        );
    }
}
