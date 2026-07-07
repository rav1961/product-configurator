<?php

declare(strict_types=1);

namespace Modules\Configurator\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Testing\TestResponse;
use Modules\Catalog\Domain\Enums\ProductStatus;
use Modules\Catalog\Domain\Models\Product;
use Modules\Configurator\Domain\Enums\AttributeType;
use Modules\Configurator\Domain\Enums\DependencyAction;
use Modules\Configurator\Domain\Enums\DependencyCondition;
use Modules\Configurator\Domain\Models\Attribute;
use Modules\Configurator\Domain\Models\AttributeValue;
use Modules\Configurator\Domain\Models\Dependency;
use Modules\Configurator\Domain\Models\Step;
use Modules\Configurator\Tests\Concerns\BuildsConfiguratorFixtures;
use Modules\Users\Domain\Models\User;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

final class ConfiguratorApiTest extends TestCase
{
    use BuildsConfiguratorFixtures;
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    #[DataProvider('configuratorRouteProvider')]
    public function test_guest_cannot_access_configurator_api(string $routeName, string $method): void
    {
        $product = $this->configurableProduct();

        $this->configuratorRequest($routeName, $product->public_id, $method)
            ->assertUnauthorized();
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function configuratorRouteProvider(): iterable
    {
        yield 'schema' => ['api.products.configurator.schema', 'GET'];
        yield 'evaluate' => ['api.products.configurator.evaluate', 'POST'];
        yield 'validate' => ['api.products.configurator.validate', 'POST'];
    }

    public function test_schema_returns_steps_and_attributes(): void
    {
        ['product' => $product] = $this->colorFinishShowWhenRed();

        $response = $this->actingAs($this->user)
            ->configuratorRequest('api.products.configurator.schema', $product->public_id, 'GET')
            ->assertOk();

        $attributes = $response->json('data.steps.0.attributes');
        $this->assertIsArray($attributes);
        $this->assertEqualsCanonicalizing(
            ['color', 'finish'],
            array_column($attributes, 'key'),
        );
    }

    public function test_evaluate_and_validate_follow_show_dependency(): void
    {
        ['product' => $product, 'color' => $color, 'finish' => $finish] = $this->colorFinishShowWhenRed();

        $this->actingAs($this->user)
            ->configuratorRequest('api.products.configurator.evaluate', $product->public_id, 'POST', [
                'selection' => [$color->public_id => 'blue'],
            ])
            ->assertOk()
            ->assertJsonPath("data.attributes.{$finish->public_id}.visible", false);

        $this->actingAs($this->user)
            ->configuratorRequest('api.products.configurator.evaluate', $product->public_id, 'POST', [
                'selection' => [$color->public_id => 'red'],
            ])
            ->assertOk()
            ->assertJsonPath("data.attributes.{$finish->public_id}.visible", true);

        $this->actingAs($this->user)
            ->configuratorRequest('api.products.configurator.validate', $product->public_id, 'POST', [
                'selection' => [$color->public_id => 'red'],
            ])
            ->assertOk()
            ->assertJsonPath('data.valid', false)
            ->assertJsonStructure(['data' => ['errors' => [$finish->public_id]]]);

        $this->actingAs($this->user)
            ->configuratorRequest('api.products.configurator.validate', $product->public_id, 'POST', [
                'selection' => [
                    $color->public_id => 'red',
                    $finish->public_id => 'matte',
                ],
            ])
            ->assertOk()
            ->assertJsonPath('data.valid', true);
    }

    public function test_duplicate_keys_across_steps_are_handled_independently(): void
    {
        $product = $this->configurableProduct();
        $step1 = Step::factory()->for($product)->create(['position' => 0]);
        $step2 = Step::factory()->for($product)->create(['position' => 1]);

        $colorStep1 = Attribute::factory()->for($step1)->select()->create(['key' => 'color']);
        $colorStep2 = Attribute::factory()->for($step2)->select()->create(['key' => 'color']);

        AttributeValue::factory()->for($colorStep1)->create(['value' => 'red']);
        AttributeValue::factory()->for($colorStep2)->create(['value' => 'blue']);

        $this->actingAs($this->user)
            ->configuratorRequest('api.products.configurator.evaluate', $product->public_id, 'POST', [
                'selection' => [
                    $colorStep1->public_id => 'red',
                    $colorStep2->public_id => 'blue',
                ],
            ])
            ->assertOk()
            ->assertJsonPath("data.attributes.{$colorStep1->public_id}.visible", true)
            ->assertJsonPath("data.attributes.{$colorStep2->public_id}.visible", true)
            ->assertJsonPath("data.attributes.{$colorStep1->public_id}.key", 'color')
            ->assertJsonPath("data.attributes.{$colorStep2->public_id}.key", 'color');
    }

    #[DataProvider('configuratorRouteProvider')]
    public function test_non_configurable_product_returns_422(string $routeName, string $method): void
    {
        $product = Product::factory()->active()->create(['is_configurable' => false]);

        $this->actingAs($this->user)
            ->configuratorRequest($routeName, $product->public_id, $method)
            ->assertUnprocessable();
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return TestResponse<Response>
     */
    private function configuratorRequest(
        string $routeName,
        string $productPublicId,
        string $method,
        array $payload = ['selection' => []],
    ): TestResponse {
        $url = route($routeName, ['productId' => $productPublicId]);

        return $method === 'GET'
            ? $this->getJson($url)
            : $this->postJson($url, $payload);
    }

    public function test_unverified_user_is_forbidden_on_all_configurator_routes(): void
    {
        $product = $this->configurableProduct();

        foreach (self::configuratorRouteProvider() as [$routeName, $method]) {
            $this->actingAs(User::factory()->unverified()->create())
                ->configuratorRequest($routeName, $product->public_id, $method)
                ->assertForbidden();
        }
    }

    #[DataProvider('configuratorRouteProvider')]
    public function test_unknown_product_returns_not_found(string $routeName, string $method): void
    {
        $this->actingAs($this->user)
            ->configuratorRequest(
                $routeName,
                '01JAAAAAAAAAAAAAAAAAAAAAAAAA',
                $method,
            )
            ->assertNotFound();
    }

    #[DataProvider('configuratorRouteProvider')]
    public function test_inactive_configurable_product_returns_not_found(string $routeName, string $method): void
    {
        $product = Product::factory()->configurable()->create([
            'status' => ProductStatus::Draft,
        ]);

        $this->actingAs($this->user)
            ->configuratorRequest($routeName, $product->public_id, $method)
            ->assertNotFound();
    }

    public function test_evaluate_applies_require_action(): void
    {
        $product = $this->configurableProduct();
        $step = Step::factory()->for($product)->create();
        $color = Attribute::factory()->for($step)->select()->create(['key' => 'color']);
        $notes = Attribute::factory()->for($step)->create([
            'key' => 'notes',
            'type' => AttributeType::Text,
            'is_required' => false,
        ]);

        AttributeValue::factory()->for($color)->create(['value' => 'red']);

        Dependency::factory()->create([
            'product_id' => $product->id,
            'source_attribute_id' => $color->id,
            'target_attribute_id' => $notes->id,
            'condition' => DependencyCondition::Equals,
            'condition_value' => 'red',
            'action' => DependencyAction::Require,
        ]);

        $this->actingAs($this->user)
            ->configuratorRequest('api.products.configurator.evaluate', $product->public_id, 'POST', [
                'selection' => [$color->public_id => 'red'],
            ])
            ->assertOk()
            ->assertJsonPath("data.attributes.{$notes->public_id}.required", true);
    }

    public function test_disable_action_blocks_values_on_validate(): void
    {
        $product = $this->configurableProduct();
        $step = Step::factory()->for($product)->create();
        $color = Attribute::factory()->for($step)->select()->create(['key' => 'color']);
        $finish = Attribute::factory()->for($step)->create([
            'key' => 'finish',
            'type' => AttributeType::Text,
            'is_required' => false,
        ]);

        AttributeValue::factory()->for($color)->create(['value' => 'red']);

        Dependency::factory()->create([
            'product_id' => $product->id,
            'source_attribute_id' => $color->id,
            'target_attribute_id' => $finish->id,
            'condition' => DependencyCondition::Equals,
            'condition_value' => 'red',
            'action' => DependencyAction::Disable,
        ]);

        $this->actingAs($this->user)
            ->configuratorRequest('api.products.configurator.evaluate', $product->public_id, 'POST', [
                'selection' => [
                    $color->public_id => 'red',
                    $finish->public_id => 'matte',
                ],
            ])
            ->assertOk()
            ->assertJsonPath("data.attributes.{$finish->public_id}.disabled", true);

        $this->actingAs($this->user)
            ->configuratorRequest('api.products.configurator.validate', $product->public_id, 'POST', [
                'selection' => [
                    $color->public_id => 'red',
                    $finish->public_id => 'matte',
                ],
            ])
            ->assertOk()
            ->assertJsonPath('data.valid', false)
            ->assertJsonStructure(['data' => ['errors' => [$finish->public_id]]]);
    }

    public function test_evaluate_applies_hide_action(): void
    {
        $product = $this->configurableProduct();
        $step = Step::factory()->for($product)->create();
        $color = Attribute::factory()->for($step)->select()->create(['key' => 'color']);
        $finish = Attribute::factory()->for($step)->create([
            'key' => 'finish',
            'type' => AttributeType::Text,
            'is_required' => false,
        ]);

        AttributeValue::factory()->for($color)->create(['value' => 'red']);

        Dependency::factory()->create([
            'product_id' => $product->id,
            'source_attribute_id' => $color->id,
            'target_attribute_id' => $finish->id,
            'condition' => DependencyCondition::Equals,
            'condition_value' => 'red',
            'action' => DependencyAction::Hide,
        ]);

        $this->actingAs($this->user)
            ->configuratorRequest('api.products.configurator.evaluate', $product->public_id, 'POST', [
                'selection' => [$color->public_id => 'red'],
            ])
            ->assertOk()
            ->assertJsonPath("data.attributes.{$finish->public_id}.visible", false);
    }
}
