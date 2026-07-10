<?php

declare(strict_types=1);

namespace Modules\Configurator\Infrastructure\Persistence\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Catalog\Domain\Models\Product;
use Modules\Configurator\Domain\Enums\DependencyAction;
use Modules\Configurator\Domain\Models\Attribute;
use Modules\Shared\Domain\Enums\SelectionCondition;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('configurator_dependencies', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id');
            $table->foreignIdFor(Product::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignIdFor(Attribute::class, 'source_attribute_id')
                ->constrained('configurator_attributes')
                ->cascadeOnDelete();
            $table->string('condition')->default(SelectionCondition::Equals->value);
            $table->string('condition_value')->nullable();
            $table->foreignIdFor(Attribute::class, 'target_attribute_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('action')->default(DependencyAction::Show->value);
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();

            $table->index(['product_id', 'position']);
            $table->index('source_attribute_id');
            $table->index('target_attribute_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configurator_dependencies');
    }
};
