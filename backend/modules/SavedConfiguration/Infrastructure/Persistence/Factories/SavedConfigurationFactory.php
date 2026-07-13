<?php

declare(strict_types=1);

namespace Modules\SavedConfiguration\Infrastructure\Persistence\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Catalog\Domain\Models\Product;
use Modules\SavedConfiguration\Domain\Enums\SavedConfigurationStatus;
use Modules\SavedConfiguration\Domain\Models\SavedConfiguration;
use Modules\Users\Domain\Models\User;

/**
 * @extends Factory<SavedConfiguration>
 */
final class SavedConfigurationFactory extends Factory
{
    protected $model = SavedConfiguration::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'product_id' => Product::factory()->active()->configurable(),
            'status' => SavedConfigurationStatus::Draft,
            'selection' => [],
            'price' => [],
            'effects' => [
                'modifiers' => [],
                'overrides' => [],
                'excludedOptions' => [],
                'messages' => [],
            ],
        ];
    }

    public function configure(): SavedConfigurationFactory
    {
        return $this->afterCreating(
            function (SavedConfiguration $savedConfiguration): void {
                $basePrice = fake()->numberBetween(50000, 500000);

                $savedConfiguration->update([
                    'price' => [
                        'productId' => $savedConfiguration->product->public_id,
                        'basePrice' => $basePrice,
                        'total' => $basePrice,
                        'hasOverride' => false,
                    ],
                ]);
            });
    }
}
