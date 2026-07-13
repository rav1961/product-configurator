<?php

declare(strict_types=1);

namespace Modules\Pricing\Infrastructure\Persistence\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Catalog\Domain\Models\Product;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pricing_product_prices', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(Product::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->unsignedInteger('amount');
            $table->timestamps();

            $table->unique('product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pricing_product_prices');
    }
};
