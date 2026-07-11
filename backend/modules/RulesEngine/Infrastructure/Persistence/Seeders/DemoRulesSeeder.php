<?php

declare(strict_types=1);

namespace Modules\RulesEngine\Infrastructure\Persistence\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Modules\Catalog\Domain\Models\Product;
use Modules\Configurator\Domain\Models\Attribute;
use Modules\RulesEngine\Domain\Enums\MatchMode;
use Modules\RulesEngine\Domain\Enums\RuleActionType;
use Modules\RulesEngine\Domain\Models\Rule;
use Modules\RulesEngine\Domain\Models\RuleAction;
use Modules\RulesEngine\Domain\Models\RuleCondition;
use Modules\RulesEngine\Domain\Models\RuleGroup;
use Modules\Shared\Domain\Enums\SelectionCondition;

final class DemoRulesSeeder extends Seeder
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
                $this->seedProductRules($productDefinition);
            }
        }
    }

    /**
     * @param  array<string, mixed>  $definition
     */
    private function seedProductRules(array $definition): void
    {
        /** @var list<array<string, mixed>> $rules */
        $rules = $definition['configuration']['rules'] ?? [];

        if ($rules === []) {
            return;
        }

        $product = Product::query()
            ->where('slug', (string) $definition['slug'])
            ->first();

        if ($product === null) {
            throw new \RuntimeException(sprintf(
                'Demo rules seeder: product [%s] not found. Run DemoConfiguratorSeeder first.',
                (string) $definition['slug'],
            ));
        }

        $product->rules()->delete();

        /** @var Collection<string, Attribute> $attributesByKey */
        $attributesByKey = Attribute::query()
            ->whereHas('step', fn ($query) => $query->where('product_id', $product->id))
            ->get()
            ->keyBy('key');

        foreach ($rules as $ruleDefinition) {
            $this->seedRule($product->id, $ruleDefinition, $attributesByKey, (string) $definition['slug']);
        }
    }

    /**
     * @param  array<string, mixed>  $definition
     * @param  Collection<string, Attribute>  $attributesByKey
     */
    private function seedRule(int $productId, array $definition, Collection $attributesByKey, string $productSlug): void
    {
        $rule = Rule::query()->create([
            'public_id' => (string) Str::ulid(),
            'product_id' => $productId,
            'name' => (string) $definition['name'],
            'description' => $definition['description'] ?? null,
            'groups_match_mode' => MatchMode::from((string) ($definition['groups_match_mode'] ?? 'all')),
            'position' => (int) ($definition['position'] ?? 0),
            'is_active' => (bool) ($definition['is_active'] ?? true),
        ]);

        foreach ($definition['groups'] ?? [] as $groupDefinition) {
            $group = RuleGroup::query()->create([
                'public_id' => (string) Str::ulid(),
                'rule_id' => $rule->id,
                'conditions_match_mode' => MatchMode::from((string) ($groupDefinition['conditions_match_mode'] ?? 'all')),
                'position' => (int) ($groupDefinition['position'] ?? 0),
            ]);

            foreach ($groupDefinition['conditions'] ?? [] as $conditionDefinition) {
                $sourceKey = (string) $conditionDefinition['source'];
                $source = $attributesByKey->get($sourceKey);

                if ($source === null) {
                    throw new \RuntimeException(sprintf(
                        'Demo rules: unknown source attribute [%s] on product [%s].',
                        $sourceKey,
                        $productSlug,
                    ));
                }

                RuleCondition::query()->create([
                    'public_id' => (string) Str::ulid(),
                    'rule_group_id' => $group->id,
                    'source_attribute_id' => $source->id,
                    'condition' => SelectionCondition::from((string) $conditionDefinition['condition']),
                    'condition_value' => $conditionDefinition['condition_value'] ?? null,
                    'position' => (int) ($conditionDefinition['position'] ?? 0),
                ]);
            }
        }

        foreach ($definition['actions'] ?? [] as $actionDefinition) {
            RuleAction::query()->create([
                'public_id' => (string) Str::ulid(),
                'rule_id' => $rule->id,
                'type' => RuleActionType::from((string) $actionDefinition['type']),
                'payload' => $this->resolvePayload(
                    $actionDefinition['payload'] ?? [],
                    $attributesByKey,
                    $productSlug,
                ),
                'position' => (int) ($actionDefinition['position'] ?? 0),
            ]);
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  Collection<string, Attribute>  $attributesByKey
     * @return array<string, mixed>
     */
    private function resolvePayload(array $payload, Collection $attributesByKey, string $productSlug): array
    {
        if (! isset($payload['attribute_key'])) {
            return $payload;
        }

        $attributeKey = (string) $payload['attribute_key'];

        unset($payload['attribute_key']);

        $attribute = $attributesByKey->get($attributeKey);

        if ($attribute === null) {
            throw new \RuntimeException(sprintf(
                'Demo rules: unknown payload attribute_key [%s] on product [%s].',
                $attributeKey,
                $productSlug,
            ));
        }

        $payload['attribute_id'] = $attribute->public_id;

        return $payload;
    }
}
