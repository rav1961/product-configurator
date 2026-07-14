<?php

declare(strict_types=1);

namespace Modules\SavedConfiguration\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
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
use Modules\SavedConfiguration\Domain\Enums\SavedConfigurationStatus;
use Modules\SavedConfiguration\Domain\Models\SavedConfiguration;
use Modules\Shared\Domain\Enums\SelectionCondition;
use Modules\Users\Domain\Models\User;
use Tests\TestCase;

final class SavedConfigurationApiTest extends TestCase
{
    use BuildsRulesFixtures;
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_guest_cannot_create_saved_configuration(): void
    {
        $product = $this->ruleProduct();
        ProductPrice::factory()->for($product)->create();

        $this->storeRequest([
            'productId' => $product->public_id,
            'selection' => [],
        ])->assertUnauthorized();
    }

    public function test_unverified_user_cannot_create_saved_configuration(): void
    {
        $product = $this->ruleProduct();
        ProductPrice::factory()->for($product)->create();

        $this->actingAs(User::factory()->unverified()->create())
            ->storeRequest([
                'productId' => $product->public_id,
                'selection' => [],
            ])
            ->assertForbidden();
    }

    public function test_store_requires_product_id_and_selection(): void
    {
        $this->actingAs($this->user)
            ->storeRequest([])
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'productId',
                'selection',
            ]);
    }

    public function test_store_requires_valid_product_ulid(): void
    {
        $this->actingAs($this->user)
            ->storeRequest([
                'productId' => 'invalid-product-id',
                'selection' => [],
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['productId']);
    }

    public function test_unknown_product_returns_not_found(): void
    {
        $this->actingAs($this->user)
            ->storeRequest([
                'productId' => (string) Str::ulid(),
                'selection' => [],
            ])
            ->assertNotFound();
    }

    public function test_inactive_product_returns_not_found(): void
    {
        $product = Product::factory()->configurable()->create([
            'status' => ProductStatus::Draft,
        ]);
        ProductPrice::factory()->for($product)->create();

        $this->actingAs($this->user)
            ->storeRequest([
                'productId' => $product->public_id,
                'selection' => [],
            ])
            ->assertNotFound();
    }

    public function test_non_configurable_product_returns_unprocessable(): void
    {
        $product = Product::factory()->active()->create([
            'is_configurable' => false,
        ]);
        ProductPrice::factory()->for($product)->create();

        $this->actingAs($this->user)
            ->storeRequest([
                'productId' => $product->public_id,
                'selection' => [],
            ])
            ->assertUnprocessable();
    }

    public function test_product_without_base_price_returns_unprocessable(): void
    {
        $product = $this->ruleProduct();

        $this->actingAs($this->user)
            ->storeRequest([
                'productId' => $product->public_id,
                'selection' => [],
            ])
            ->assertUnprocessable();
    }

    public function test_user_can_save_partial_configuration(): void
    {
        ['product' => $product, 'attribute' => $attribute] =
            $this->productWithAttribute();

        ProductPrice::factory()
            ->for($product)
            ->create(['amount' => 199_900]);

        $selection = [
            $attribute->public_id => 'red',
        ];

        $response = $this->actingAs($this->user)
            ->storeRequest([
                'productId' => $product->public_id,
                'selection' => $selection,
            ])
            ->assertCreated()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'productId',
                    'status',
                    'selection',
                    'price' => [
                        'productId',
                        'basePrice',
                        'total',
                        'hasOverride',
                    ],
                    'effects' => [
                        'modifiers',
                        'overrides',
                        'excludedOptions',
                        'messages',
                    ],
                    'createdAt',
                ],
            ])
            ->assertJsonPath('data.productId', $product->public_id)
            ->assertJsonPath('data.status', SavedConfigurationStatus::Draft->value)
            ->assertJsonPath(
                "data.selection.{$attribute->public_id}",
                'red',
            )
            ->assertJsonPath('data.price.basePrice', 199900)
            ->assertJsonPath('data.price.total', 199900)
            ->assertJsonPath('data.price.hasOverride', false);

        $savedConfiguration = SavedConfiguration::query()
            ->where('public_id', $response->json('data.id'))
            ->firstOrFail();

        $this->assertSame($this->user->id, $savedConfiguration->user_id);
        $this->assertSame($product->id, $savedConfiguration->product_id);
        $this->assertSame($selection, $savedConfiguration->selection);
        $this->assertSame(
            SavedConfigurationStatus::Draft,
            $savedConfiguration->status,
        );
        $this->assertSame(199900, $savedConfiguration->price['basePrice']);
        $this->assertSame(199900, $savedConfiguration->price['total']);
    }

    public function test_user_can_read_own_saved_configuration(): void
    {
        $product = $this->ruleProduct();

        $savedConfiguration = SavedConfiguration::factory()
            ->for($this->user)
            ->for($product)
            ->create([
                'selection' => ['attribute-id' => 'red'],
                'price' => [
                    'productId' => $product->public_id,
                    'basePrice' => 199900,
                    'total' => 219899,
                    'hasOverride' => false,
                ],
                'effects' => [
                    'modifiers' => [],
                    'overrides' => [],
                    'excludedOptions' => [],
                    'messages' => [],
                ],
            ]);

        $this->actingAs($this->user)
            ->showRequest($savedConfiguration->public_id)
            ->assertOk()
            ->assertJsonPath('data.id', $savedConfiguration->public_id)
            ->assertJsonPath('data.productId', $product->public_id)
            ->assertJsonPath('data.status', SavedConfigurationStatus::Draft->value)
            ->assertJsonPath('data.selection.attribute-id', 'red')
            ->assertJsonPath('data.price.basePrice', 199900)
            ->assertJsonPath('data.price.total', 219899);
    }

    public function test_guest_cannot_read_saved_configuration(): void
    {
        $savedConfiguration = SavedConfiguration::factory()->create();

        $this->showRequest($savedConfiguration->public_id)
            ->assertUnauthorized();
    }

    public function test_unverified_user_cannot_read_saved_configuration(): void
    {
        $savedConfiguration = SavedConfiguration::factory()
            ->for($this->user)
            ->create();

        $this->actingAs(User::factory()->unverified()->create())
            ->showRequest($savedConfiguration->public_id)
            ->assertForbidden();
    }

    public function test_user_cannot_read_another_users_saved_configuration(): void
    {
        $owner = User::factory()->create();

        $savedConfiguration = SavedConfiguration::factory()
            ->for($owner)
            ->create();

        $this->actingAs($this->user)
            ->showRequest($savedConfiguration->public_id)
            ->assertNotFound();
    }

    public function test_unknown_saved_configuration_returns_not_found(): void
    {
        $this->actingAs($this->user)
            ->showRequest('01JAAAAAAAAAAAAAAAAAAAAAAAAA')
            ->assertNotFound();
    }

    public function test_price_and_effects_are_immutable_snapshots(): void
    {
        ['product' => $product, 'attribute' => $attribute] =
            $this->productWithAttribute();

        $productPrice = ProductPrice::factory()
            ->for($product)
            ->create(['amount' => 199_900]);

        $rule = Rule::factory()->for($product)->create();
        $group = RuleGroup::factory()->for($rule)->create();

        RuleCondition::factory()->for($group)->create([
            'source_attribute_id' => $attribute->id,
            'condition' => SelectionCondition::Equals,
            'condition_value' => 'red',
        ]);

        $action = RuleAction::factory()->for($rule)->create([
            'type' => RuleActionType::AddModifier,
            'payload' => [
                'amount' => 19_999,
                'operation' => 'add',
                'label' => 'Red finish',
            ],
        ]);

        $storeResponse = $this->actingAs($this->user)
            ->storeRequest([
                'productId' => $product->public_id,
                'selection' => [
                    $attribute->public_id => 'red',
                ],
            ])
            ->assertCreated()
            ->assertJsonPath('data.price.basePrice', 199900)
            ->assertJsonPath('data.price.total', 219899)
            ->assertJsonPath(
                'data.effects.modifiers.0.amountMinor',
                19999,
            );

        $savedConfigurationId = $storeResponse->json('data.id');

        $productPrice->update([
            'amount' => 999900,
        ]);

        $action->update([
            'payload' => [
                'amount' => 50000,
                'operation' => 'add',
                'label' => 'Changed modifier',
            ],
        ]);

        $this->actingAs($this->user)
            ->showRequest($savedConfigurationId)
            ->assertOk()
            ->assertJsonPath('data.price.basePrice', 199900)
            ->assertJsonPath('data.price.total', 219899)
            ->assertJsonPath(
                'data.effects.modifiers.0.amountMinor',
                19999,
            )
            ->assertJsonPath(
                'data.effects.modifiers.0.label',
                'Red finish',
            );
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return TestResponse<Response>
     */
    private function storeRequest(array $payload): TestResponse
    {
        return $this->postJson(
            route('api.saved-configuration.store'),
            $payload,
        );
    }

    /**
     * @return TestResponse<Response>
     */
    private function showRequest(string $savedConfigurationId): TestResponse
    {
        return $this->getJson(
            route('api.saved-configuration.show', [
                'savedConfigurationId' => $savedConfigurationId,
            ]),
        );
    }
}
