<?php

declare(strict_types=1);

namespace Modules\Configurator\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Testing\TestResponse;
use Modules\Catalog\Domain\Models\Product;
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
        ['product' => $product] = $this->colorFinishShowWhenRed();

        $this->actingAs($this->user)
            ->configuratorRequest('api.products.configurator.evaluate', $product->public_id, 'POST', [
                'selection' => ['color' => 'blue'],
            ])
            ->assertOk()
            ->assertJsonPath('data.attributes.finish.visible', false);

        $this->actingAs($this->user)
            ->configuratorRequest('api.products.configurator.evaluate', $product->public_id, 'POST', [
                'selection' => ['color' => 'red'],
            ])
            ->assertOk()
            ->assertJsonPath('data.attributes.finish.visible', true);

        $this->actingAs($this->user)
            ->configuratorRequest('api.products.configurator.validate', $product->public_id, 'POST', [
                'selection' => ['color' => 'red'],
            ])
            ->assertOk()
            ->assertJsonPath('data.valid', false)
            ->assertJsonStructure(['data' => ['errors' => ['finish']]]);

        $this->actingAs($this->user)
            ->configuratorRequest('api.products.configurator.validate', $product->public_id, 'POST', [
                'selection' => ['color' => 'red', 'finish' => 'matte'],
            ])
            ->assertOk()
            ->assertJsonPath('data.valid', true);
    }

    public function test_non_configurable_product_returns_422(): void
    {
        $product = Product::factory()->active()->create(['is_configurable' => false]);

        $this->actingAs($this->user)
            ->configuratorRequest('api.products.configurator.schema', $product->public_id, 'GET')
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
}
