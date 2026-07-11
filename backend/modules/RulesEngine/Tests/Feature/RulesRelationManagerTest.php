<?php

declare(strict_types=1);

namespace Modules\RulesEngine\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Catalog\Domain\Models\Product;
use Modules\Catalog\Presentation\Filament\Resources\ProductResource\Pages\EditProduct;
use Modules\RulesEngine\Presentation\Filament\RelationManagers\RulesRelationManager;
use Modules\Users\Domain\Enums\Role;
use Modules\Users\Domain\Models\User;
use Modules\Users\Infrastructure\Persistence\Seeders\RoleSeeder;
use Tests\TestCase;

final class RulesRelationManagerTest extends TestCase
{
    use RefreshDatabase;

    public function test_visible_only_for_configurable_products(): void
    {
        $this->seed(RoleSeeder::class);

        $this->actingAs(User::factory()->create()->assignRole(Role::Manager->value));

        $configurable = Product::factory()->configurable()->create();
        $standard = Product::factory()->create(['is_configurable' => false]);

        $this->assertTrue(
            RulesRelationManager::canViewForRecord($configurable, EditProduct::class),
        );
        $this->assertFalse(
            RulesRelationManager::canViewForRecord($standard, EditProduct::class),
        );
    }

    public function test_title_uses_rules_navigation_label(): void
    {
        $product = Product::factory()->configurable()->create();

        $this->assertSame(
            __('rules_engine.navigation.rules'),
            RulesRelationManager::getTitle($product, EditProduct::class),
        );
    }

    public function test_uses_product_rules_relationship(): void
    {
        $this->assertSame('rules', RulesRelationManager::getRelationshipName());
    }
}
