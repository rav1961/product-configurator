<?php

declare(strict_types=1);

namespace Modules\Configurator\Application\DTO;

use Illuminate\Support\Collection;
use Modules\Catalog\Application\DTO\ConfigurableProductData;
use Modules\Configurator\Domain\Models\Step;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

final class ConfiguratorSchemaData extends Data
{
    /**
     * @param  DataCollection<int, ConfigurationStepData>  $steps
     */
    public function __construct(
        public string $productId,
        public string $productName,
        #[DataCollectionOf(ConfigurationStepData::class)]
        public DataCollection $steps,
    ) {}

    /**
     * @param  Collection<int, Step>  $steps
     */
    public static function fromGraph(
        ConfigurableProductData $product,
        Collection $steps
    ): self {
        $stepData = ConfigurationStepData::collect(
            $steps
                ->map(static fn (Step $step): ConfigurationStepData => ConfigurationStepData::fromModel($step))
                ->values()
                ->all(),
            DataCollection::class,
        )->withoutWrapping();

        return new self(
            productId: $product->id,
            productName: $product->name,
            steps: $stepData,
        );
    }

    /**
     * @return list<ConfigurationAttributeData>
     */
    public function allAttributes(): array
    {
        /** @var list<ConfigurationAttributeData> $attributes */
        $attributes = [];

        foreach ($this->steps as $step) {
            foreach ($step->attributes as $attribute) {
                $attributes[] = $attribute;
            }
        }

        return $attributes;
    }

    /**
     * @return array<string, ConfigurationAttributeData>
     */
    public function getAttributeById(): array
    {
        $indexed = [];

        foreach ($this->allAttributes() as $attribute) {
            $indexed[$attribute->id] = $attribute;
        }

        return $indexed;
    }
}
