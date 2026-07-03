<?php

declare(strict_types=1);

namespace Modules\Configurator\Infrastructure\Persistence\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Configurator\Domain\Enums\AttributeType;
use Modules\Configurator\Domain\Models\Step;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('configurator_attributes', function (Blueprint $table) {
            $table->id();
            $table->ulid('public_id')->unique();

            $table->foreignIdFor(Step::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->string('name');
            $table->string('key');
            $table->string('type')->default(AttributeType::Text->value);
            $table->unsignedInteger('position')->default(0);
            $table->boolean('is_required')->default(false);
            $table->timestamps();

            $table->unique(['step_id', 'key']);
            $table->index(['step_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configurator_attributes');
    }
};
