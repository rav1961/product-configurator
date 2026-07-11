<?php

declare(strict_types=1);

namespace Modules\RulesEngine\Infrastructure\Persistence\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Catalog\Domain\Models\Product;
use Modules\RulesEngine\Domain\Enums\MatchMode;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rules_engine_rules', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id');
            $table->foreignIdFor(Product::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('groups_match_mode')->default(MatchMode::All->value);
            $table->unsignedInteger('position')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['product_id', 'position']);
            $table->index(['product_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rules_engine_rules');
    }
};
