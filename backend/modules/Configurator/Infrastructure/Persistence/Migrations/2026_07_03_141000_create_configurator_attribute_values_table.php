<?php

declare(strict_types=1);

namespace Modules\Configurator\Infrastructure\Persistence\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Configurator\Domain\Models\Attribute;
use Modules\Configurator\Domain\Models\AttributeCollection;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('configurator_attribute_values', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id')->unique();
            $table->foreignIdFor(Attribute::class)
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignIdFor(AttributeCollection::class, 'collection_id')
                ->nullable()
                ->constrained('configurator_attribute_collections')
                ->cascadeOnDelete();
            $table->string('label');
            $table->string('value');
            $table->unsignedInteger('position')->default(0);
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->index(['attribute_id', 'position']);
            $table->index(['collection_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configurator_attribute_values');
    }
};
