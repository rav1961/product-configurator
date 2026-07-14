<?php

declare(strict_types=1);

namespace Modules\SavedConfiguration\Application\DTO;

use DateTimeInterface;
use Modules\SavedConfiguration\Domain\Models\SavedConfiguration;
use Spatie\LaravelData\Data;

final class SavedConfigurationData extends Data
{
    /**
     * @param  array<string, mixed>  $selection
     * @param array{
     *     productId: string,
     *     basePrice: int,
     *     total: int,
     *     hasOverride: bool
     * } $price
     * @param array{
     *     modifiers: list<array<string, mixed>>,
     *     overrides: list<array<string, mixed>>,
     *     excludedOptions: list<array<string, mixed>>,
     *     messages: list<array<string, mixed>>
     * } $effects
     */
    public function __construct(
        public string $id,
        public string $productId,
        public string $status,
        public array $selection,
        public array $price,
        public array $effects,
        public string $createdAt,
    ) {}

    public static function fromModel(SavedConfiguration $configuration): self
    {
        return new self(
            id: $configuration->public_id,
            productId: $configuration->product->public_id,
            status: $configuration->status->value,
            selection: $configuration->selection,
            price: $configuration->price,
            effects: $configuration->effects,
            createdAt: $configuration->created_at->format(DateTimeInterface::ATOM),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'productId' => $this->productId,
            'status' => $this->status,
            'selection' => $this->selection,
            'price' => $this->price,
            'effects' => $this->effects,
            'createdAt' => $this->createdAt,
        ];
    }
}
