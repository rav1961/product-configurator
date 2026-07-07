<?php

declare(strict_types=1);

namespace Modules\Configurator\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Configurator\Tests\Concerns\BuildsConfiguratorFixtures;
use Modules\Users\Domain\Models\User;
use Tests\TestCase;

final class ConfigurationSelectionRequestTest extends TestCase
{
    use BuildsConfiguratorFixtures;
    use RefreshDatabase;

    public function test_selection_is_required_in_request_body(): void
    {
        $product = $this->configurableProduct();
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson(route('api.products.configurator.validate', ['productId' => $product->public_id]), [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['selection']);
    }

    public function test_selection_cannot_exceed_hundred_keys(): void
    {
        $product = $this->configurableProduct();
        $user = User::factory()->create();

        $selection = [];

        for ($index = 0; $index < 101; $index++) {
            $selection[sprintf('01J%026d', $index)] = 'value';
        }

        $this->actingAs($user)
            ->postJson(route('api.products.configurator.validate', ['productId' => $product->public_id]), [
                'selection' => $selection,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['selection']);
    }
}
