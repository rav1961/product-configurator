<?php

declare(strict_types=1);

namespace Modules\Configurator\Infrastructure\Persistence\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Modules\Catalog\Domain\Enums\ProductStatus;
use Modules\Catalog\Domain\Models\Category;
use Modules\Catalog\Domain\Models\Product;
use Modules\Configurator\Domain\Enums\AttributeType;
use Modules\Configurator\Domain\Enums\DependencyAction;
use Modules\Configurator\Domain\Enums\DependencyCondition;
use Modules\Configurator\Domain\Models\Attribute;
use Modules\Configurator\Domain\Models\AttributeCollection;
use Modules\Configurator\Domain\Models\AttributeValue;
use Modules\Configurator\Domain\Models\Dependency;
use Modules\Configurator\Domain\Models\Step;
use Modules\Shared\Domain\Enums\MediaCollection;
use RuntimeException;
use Spatie\MediaLibrary\HasMedia;

final class DemoConfiguratorSeeder extends Seeder
{
    public function run(): void
    {
        $catalog = config('demo-catalog');

        if (! is_array($catalog) || ! is_array($catalog['categories'] ?? null)) {
            throw new RuntimeException('Demo catalog configuration is missing or invalid.');
        }

        /** @var list<array<string, mixed>> $categories */
        $categories = $catalog['categories'];

        foreach ($categories as $categoryDefinition) {
            $this->seedCategory($categoryDefinition);
        }
    }

    /**
     * @param  array<string, mixed>  $definition
     */
    private function seedCategory(array $definition): void
    {
        $category = Category::query()->updateOrCreate(
            ['slug' => (string) $definition['slug']],
            [
                'name' => (string) $definition['name'],
                'description' => $definition['description'] ?? null,
                'position' => (int) ($definition['position'] ?? 0),
                'is_active' => true,
            ],
        );

        $this->attachCover(
            $category,
            (string) ($definition['image_seed'] ?? $category->slug),
            $category->name,
        );

        foreach ($definition['products'] ?? [] as $productDefinition) {
            $this->seedProduct($category, $productDefinition);
        }
    }

    /**
     * @param  array<string, mixed>  $definition
     */
    private function seedProduct(Category $category, array $definition): void
    {
        $product = Product::query()->updateOrCreate(
            ['slug' => (string) $definition['slug']],
            [
                'category_id' => $category->id,
                'name' => (string) $definition['name'],
                'sku' => (string) $definition['sku'],
                'description' => $definition['description'] ?? null,
                'status' => ProductStatus::Active,
                'is_configurable' => true,
                'position' => (int) ($definition['position'] ?? 0),
            ],
        );

        $this->attachCover(
            $product,
            (string) ($definition['image_seed'] ?? $product->slug),
            $product->name,
        );

        $this->seedConfiguration($product, $definition['configuration'] ?? []);
    }

    /**
     * @param  array<string, mixed>  $configuration
     */
    private function seedConfiguration(Product $product, array $configuration): void
    {
        $product->dependencies()->delete();
        $product->steps()->delete();
        $product->attributeCollections()->delete();

        /** @var array<string, AttributeCollection> $collectionsByKey */
        $collectionsByKey = [];

        foreach ($configuration['collections'] ?? [] as $collectionDefinition) {
            $collection = AttributeCollection::query()->create([
                'product_id' => $product->id,
                'name' => (string) $collectionDefinition['name'],
                'key' => (string) $collectionDefinition['key'],
                'position' => (int) ($collectionDefinition['position'] ?? 0),
            ]);

            foreach ($collectionDefinition['values'] ?? [] as $valueDefinition) {
                AttributeValue::query()->create([
                    'collection_id' => $collection->id,
                    'label' => (string) $valueDefinition['label'],
                    'value' => (string) $valueDefinition['value'],
                    'position' => (int) ($valueDefinition['position'] ?? 0),
                    'is_default' => (bool) ($valueDefinition['is_default'] ?? false),
                ]);
            }

            $collectionsByKey[$collection->key] = $collection;
        }

        /** @var array<string, Attribute> $attributesByKey */
        $attributesByKey = [];

        foreach ($configuration['steps'] ?? [] as $stepDefinition) {
            $step = Step::query()->create([
                'product_id' => $product->id,
                'name' => (string) $stepDefinition['name'],
                'position' => (int) ($stepDefinition['position'] ?? 0),
            ]);

            foreach ($stepDefinition['attributes'] ?? [] as $attributeDefinition) {
                $collectionKey = $attributeDefinition['collection_key'] ?? null;
                $attribute = Attribute::query()->create([
                    'step_id' => $step->id,
                    'collection_id' => is_string($collectionKey)
                        ? $collectionsByKey[$collectionKey]->id
                        : null,
                    'name' => (string) $attributeDefinition['name'],
                    'key' => (string) $attributeDefinition['key'],
                    'type' => AttributeType::from((string) $attributeDefinition['type']),
                    'position' => (int) ($attributeDefinition['position'] ?? 0),
                    'is_required' => (bool) ($attributeDefinition['is_required'] ?? false),
                ]);

                foreach ($attributeDefinition['options'] ?? [] as $optionDefinition) {
                    AttributeValue::query()->create([
                        'attribute_id' => $attribute->id,
                        'label' => (string) $optionDefinition['label'],
                        'value' => (string) $optionDefinition['value'],
                        'position' => (int) ($optionDefinition['position'] ?? 0),
                        'is_default' => (bool) ($optionDefinition['is_default'] ?? false),
                    ]);
                }
                $attributesByKey[$attribute->key] = $attribute;
            }
        }

        foreach ($configuration['dependencies'] ?? [] as $dependencyDefinition) {
            $sourceKey = (string) $dependencyDefinition['source'];
            $targetKey = (string) $dependencyDefinition['target'];

            if (! isset($attributesByKey[$sourceKey], $attributesByKey[$targetKey])) {
                throw new RuntimeException(sprintf(
                    'Demo catalog dependency references unknown attributes [%s] -> [%s] on product [%s].',
                    $sourceKey,
                    $targetKey,
                    $product->slug,
                ));
            }

            Dependency::query()->create([
                'product_id' => $product->id,
                'source_attribute_id' => $attributesByKey[$sourceKey]->id,
                'target_attribute_id' => $attributesByKey[$targetKey]->id,
                'condition' => DependencyCondition::from((string) $dependencyDefinition['condition']),
                'condition_value' => $dependencyDefinition['condition_value'] ?? null,
                'action' => DependencyAction::from((string) $dependencyDefinition['action']),
                'position' => (int) ($dependencyDefinition['position'] ?? 0),
            ]);
        }
    }

    private function attachCover(HasMedia $model, string $seed, string $name): void
    {
        if ($model->hasMedia(MediaCollection::Cover->value)) {
            return;
        }

        $model->addMedia(
            UploadedFile::fake()->image(Str::slug($seed).'.jpg', 1200, 800),
        )
            ->usingName($name)
            ->toMediaCollection(MediaCollection::Cover->value);
    }
}
