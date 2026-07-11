<?php

declare(strict_types=1);

namespace Modules\RulesEngine\Tests\Feature;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Catalog\Domain\Models\Product;
use Modules\RulesEngine\Domain\Contracts\RuleGraphRepositoryInterface;
use Modules\RulesEngine\Domain\Contracts\RuleRepositoryInterface;
use Modules\RulesEngine\Domain\Models\Rule;
use Modules\RulesEngine\Domain\Models\RuleAction;
use Modules\RulesEngine\Domain\Models\RuleCondition;
use Modules\RulesEngine\Domain\Models\RuleGroup;
use Tests\TestCase;

final class RuleRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_ordered_for_product(): void
    {
        $product = Product::factory()->create();
        Rule::factory()->for($product)->create(['position' => 5]);
        Rule::factory()->for($product)->create(['position' => 1]);
        Rule::factory()->create();

        $result = app(RuleRepositoryInterface::class)->listOrderedForProduct($product->id);

        $this->assertCount(2, $result);
        $this->assertSame([1, 5], $result->pluck('position')->all());
    }

    public function test_list_active_ordered_for_product_public_id_excludes_inactive(): void
    {
        $product = Product::factory()->create();
        $active = Rule::factory()->for($product)->create(['position' => 1, 'is_active' => true]);

        Rule::factory()->for($product)->inactive()->create(['position' => 2]);
        Rule::factory()->create(['position' => 3]);

        $result = app(RuleRepositoryInterface::class)
            ->listActiveOrderedForProductPublicId($product->public_id);

        $this->assertCount(1, $result);
        $this->assertTrue($result->first()->is($active));
    }

    public function test_find_by_public_id(): void
    {
        $rule = Rule::factory()->create();

        $found = app(RuleRepositoryInterface::class)->findByPublicId($rule->public_id);

        $this->assertTrue($found->is($rule));
    }

    public function test_find_by_public_id_throws_when_unknown(): void
    {
        $this->expectException(ModelNotFoundException::class);

        app(RuleRepositoryInterface::class)->findByPublicId((string) Str::ulid());
    }

    public function test_rule_graph_repository_builds_active_graph(): void
    {
        $product = Product::factory()->create();
        $rule = Rule::factory()->for($product)->create(['position' => 1]);
        $group = RuleGroup::factory()->for($rule)->create(['position' => 1]);

        RuleCondition::factory()->for($group)->create(['position' => 1]);
        RuleAction::factory()->for($rule)->create(['position' => 1]);
        Rule::factory()->for($product)->inactive()->create();

        $graph = app(RuleGraphRepositoryInterface::class)
            ->buildActiveForProductPublicId($product->public_id);

        $this->assertCount(1, $graph);

        $loadedRule = $graph->first();

        $this->assertTrue($loadedRule->is($rule));
        $this->assertTrue($loadedRule->relationLoaded('groups'));
        $this->assertTrue($loadedRule->relationLoaded('actions'));
        $this->assertTrue($loadedRule->groups->first()->relationLoaded('conditions'));
        $this->assertTrue($loadedRule->groups->first()->conditions->first()->relationLoaded('sourceAttribute'));
    }
}
