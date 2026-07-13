<?php

declare(strict_types=1);

namespace Modules\Pricing\Infrastructure\Persistence\Seeders;

use Illuminate\Database\Seeder;
use Modules\Catalog\Domain\Models\Product;
use Modules\Pricing\Domain\Models\ProductPrice;

final class DemoPricingSeeder extends Seeder
{
    public function run(): void
    {
        $catalog = config('demo-catalog');

        if (! is_array($catalog) || ! is_array($catalog['categories'] ?? null)) {
            throw new \RuntimeException('Demo catalog configuration is missing or invalid.');
        }

        /** @var list<array<string, mixed>> $categories */
        $categories = $catalog['categories'];

        foreach ($categories as $categoryDefinition) {
            foreach ($categoryDefinition['products'] ?? [] as $productDefinition) {
                $this->seedProductPrice($productDefinition);
            }
        }
    }

    /**
     * @param  array<string, mixed>  $definition
     */
    private function seedProductPrice(array $definition): void
    {
        if (! array_key_exists('base_price', $definition)) {
            return;
        }

        $product = Product::query()
            ->where('slug', (string) $definition['slug'])
            ->first();

        if ($product === null) {
            throw new \RuntimeException(sprintf(
                'Demo pricing seeder: product [%s] not found. Run DemoConfiguratorSeeder first.',
                (string) $definition['slug'],
            ));
        }

        ProductPrice::query()->updateOrCreate(
            ['product_id' => $product->id],
            ['amount' => (int) $definition['base_price']],
        );
    }
}
