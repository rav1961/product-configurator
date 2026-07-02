<?php

declare(strict_types=1);

namespace Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Catalog\Domain\Models\Category;
use Modules\Catalog\Domain\Models\Product;
use Modules\Catalog\Presentation\Filament\Policies\CategoryPolicy;
use Modules\Catalog\Presentation\Filament\Policies\ProductPolicy;
use Modules\Users\Domain\Enums\Role;
use Modules\Users\Domain\Models\User;
use Modules\Users\Infrastructure\Persistence\Seeders\RoleSeeder;
use Tests\TestCase;

final class CatalogPolicyTest extends TestCase
{
    use RefreshDatabase;

    private CategoryPolicy $categoryPolicy;

    private ProductPolicy $productPolicy;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);

        $this->categoryPolicy = new CategoryPolicy;
        $this->productPolicy = new ProductPolicy;
    }

    public function test_admin_and_manager_can_manage_catalog(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create();

        foreach ([Role::Admin, Role::Manager] as $role) {
            $user = $this->userWithRole($role);

            $this->assertTrue($this->categoryPolicy->viewAny($user));
            $this->assertTrue($this->categoryPolicy->view($user, $category));
            $this->assertTrue($this->categoryPolicy->create($user));
            $this->assertTrue($this->categoryPolicy->update($user, $category));
            $this->assertTrue($this->categoryPolicy->delete($user, $category));
            $this->assertTrue($this->productPolicy->viewAny($user));
            $this->assertTrue($this->productPolicy->view($user, $product));
            $this->assertTrue($this->productPolicy->create($user));
            $this->assertTrue($this->productPolicy->update($user, $product));
            $this->assertTrue($this->productPolicy->delete($user, $product));
        }
    }

    public function test_sales_and_customer_cannot_manage_catalog(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create();

        foreach ([Role::Sales, Role::Customer] as $role) {
            $user = $this->userWithRole($role);

            $this->assertFalse($this->categoryPolicy->viewAny($user));
            $this->assertFalse($this->categoryPolicy->view($user, $category));
            $this->assertFalse($this->categoryPolicy->create($user));
            $this->assertFalse($this->categoryPolicy->update($user, $category));
            $this->assertFalse($this->categoryPolicy->delete($user, $category));
            $this->assertFalse($this->productPolicy->viewAny($user));
            $this->assertFalse($this->productPolicy->view($user, $product));
            $this->assertFalse($this->productPolicy->create($user));
            $this->assertFalse($this->productPolicy->update($user, $product));
            $this->assertFalse($this->productPolicy->delete($user, $product));
        }
    }

    private function userWithRole(Role $role): User
    {
        $user = User::factory()->create();

        $user->assignRole($role->value);

        return $user;
    }
}
