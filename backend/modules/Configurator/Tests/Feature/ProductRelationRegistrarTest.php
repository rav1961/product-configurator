<?php

declare(strict_types=1);

namespace Modules\Configurator\Tests\Feature;

use Modules\Catalog\Presentation\Filament\Resources\ProductResource;
use Modules\Configurator\Presentation\Filament\RelationManagers\AttributeCollectionsRelationManager;
use Modules\Configurator\Presentation\Filament\RelationManagers\DependenciesRelationManager;
use Modules\Configurator\Presentation\Filament\RelationManagers\StepsRelationManager;
use Modules\Pricing\Presentation\Filament\RelationManagers\ProductPriceRelationManager;
use Modules\RulesEngine\Presentation\Filament\RelationManagers\RulesRelationManager;
use Modules\Shared\Presentation\Filament\ProductRelationRegistrar;
use Tests\TestCase;

final class ProductRelationRegistrarTest extends TestCase
{
    public function test_product_resource_exposes_registered_relation_managers(): void
    {
        $registrar = app(ProductRelationRegistrar::class);

        $this->assertSame($registrar, app(ProductRelationRegistrar::class));
        $this->assertSame(
            [
                StepsRelationManager::class,
                AttributeCollectionsRelationManager::class,
                DependenciesRelationManager::class,
                RulesRelationManager::class,
                ProductPriceRelationManager::class,
            ],
            $registrar->all(),
        );
        $this->assertSame($registrar->all(), ProductResource::getRelations());
    }
}
