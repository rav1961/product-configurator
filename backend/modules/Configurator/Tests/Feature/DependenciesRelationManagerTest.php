<?php

declare(strict_types=1);

namespace Modules\Configurator\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Catalog\Domain\Models\Product;
use Modules\Catalog\Presentation\Filament\Resources\ProductResource\Pages\EditProduct;
use Modules\Configurator\Presentation\Filament\RelationManagers\DependenciesRelationManager;
use Modules\Users\Domain\Enums\Role;
use Modules\Users\Domain\Models\User;
use Modules\Users\Infrastructure\Persistence\Seeders\RoleSeeder;
use Tests\TestCase;

final class DependenciesRelationManagerTest extends TestCase
{
    use RefreshDatabase;

    public function test_visible_only_for_configurable_products(): void
    {
        $this->seed(RoleSeeder::class);

        $manager = User::factory()->create();
        $manager->assignRole(Role::Manager->value);

        $this->actingAs($manager);

        $configurable = Product::factory()->configurable()->create();
        $standard = Product::factory()->create(['is_configurable' => false]);

        $this->assertTrue(
            DependenciesRelationManager::canViewForRecord($configurable, EditProduct::class),
        );
        $this->assertFalse(
            DependenciesRelationManager::canViewForRecord($standard, EditProduct::class),
        );
    }

    public function test_title_uses_dependencies_navigation_label(): void
    {
        $product = Product::factory()->configurable()->create();

        $this->assertSame(
            __('configurator.navigation.dependencies'),
            DependenciesRelationManager::getTitle($product, EditProduct::class),
        );
    }

    public function test_uses_product_dependencies_relationship(): void
    {
        $this->assertSame('dependencies', DependenciesRelationManager::getRelationshipName());
    }
}
