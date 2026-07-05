<?php

declare(strict_types=1);

namespace Modules\Configurator\Application\DTO;

use Illuminate\Support\Collection;
use Modules\Catalog\Application\DTO\ConfigurableProductData;
use Modules\Configurator\Domain\Models\Step;
use Spatie\LaravelData\Data;

final class ConfiguratorSchemaData extends Data
{
    /**
     * @param  list<ConfigurationStepData>  $steps
     */
    public function __construct(
        public string $productId,
        public string $productName,
        public array $steps,
    ) {}

    /**
     * @param  Collection<int, Step>  $steps
     */
    public static function fromGraph(
        ConfigurableProductData $product,
        Collection $steps
    ): self {
        return new self(
            productId: $product->id,
            productName: $product->name,
            steps: $steps
                ->map(static fn (Step $step): ConfigurationStepData => ConfigurationStepData::fromModel($step))
                ->values()
                ->all(),
        );
    }

    /**
     * @return list<ConfigurationAttributeData>
     */
    public function allAttributes(): array
    {
        return collect($this->steps)
            ->flatMap(static fn (ConfigurationStepData $stepData): array => $stepData->attributes)
            ->values()
            ->all();
    }
}
