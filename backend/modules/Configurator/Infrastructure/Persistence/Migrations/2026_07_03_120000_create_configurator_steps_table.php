<?php

declare(strict_types=1);

namespace Module\Configurator\Infrastructure\Persistence\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Catalog\Domain\Models\Product;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('configurator_steps', function (Blueprint $table) {
            $table->id();
            $table->ulid('public_id')->unique();

            $table->foreignIdFor(Product::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->string('name');
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();

            $table->index(['product_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configurator_steps');
    }
};
