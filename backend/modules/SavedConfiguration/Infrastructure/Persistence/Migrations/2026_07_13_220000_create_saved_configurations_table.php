<?php

declare(strict_types=1);

namespace Modules\SavedConfiguration\Infrastructure\Persistence\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Catalog\Domain\Models\Product;
use Modules\Users\Domain\Models\User;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saved_configurations', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id')->unique();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Product::class)->constrained()->cascadeOnDelete();
            $table->string('status', 32)->default('draft');
            $table->json('selection');
            $table->json('price');
            $table->json('effects');
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saved_configurations');
    }
};
