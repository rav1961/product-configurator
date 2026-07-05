<?php

declare(strict_types=1);

namespace Modules\Configurator\Tests\Concerns;

use Modules\Catalog\Domain\Models\Product;
use Modules\Configurator\Domain\Enums\AttributeType;
use Modules\Configurator\Domain\Enums\DependencyAction;
use Modules\Configurator\Domain\Enums\DependencyCondition;
use Modules\Configurator\Domain\Models\Attribute;
use Modules\Configurator\Domain\Models\AttributeValue;
use Modules\Configurator\Domain\Models\Dependency;
use Modules\Configurator\Domain\Models\Step;

trait BuildsConfiguratorFixtures
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    protected function configurableProduct(array $attributes = []): Product
    {
        return Product::factory()->active()->configurable()->create($attributes);
    }

    /**
     * Color/finish pair with Show-when-red dependency — core configurator flow.
     *
     * @return array{product: Product, color: Attribute, finish: Attribute, dependency: Dependency}
     */
    protected function colorFinishShowWhenRed(?Product $product = null): array
    {
        $product ??= $this->configurableProduct();
        $step = Step::factory()->for($product)->create();
        $color = Attribute::factory()->for($step)->select()->create([
            'key' => 'color',
            'is_required' => false,
        ]);
        $finish = Attribute::factory()->for($step)->create([
            'key' => 'finish',
            'type' => AttributeType::Text,
            'is_required' => true,
        ]);

        AttributeValue::factory()->for($color)->create([
            'label' => 'Red',
            'value' => 'red',
        ]);

        $dependency = Dependency::factory()->create([
            'product_id' => $product->id,
            'source_attribute_id' => $color->id,
            'target_attribute_id' => $finish->id,
            'condition' => DependencyCondition::Equals,
            'condition_value' => 'red',
            'action' => DependencyAction::Show,
            'position' => 0,
        ]);

        return compact('product', 'color', 'finish', 'dependency');
    }
}
