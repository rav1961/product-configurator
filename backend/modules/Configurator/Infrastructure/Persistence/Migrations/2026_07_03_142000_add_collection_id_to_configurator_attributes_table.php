<?php

declare(strict_types=1);

namespace Modules\Configurator\Infrastructure\Persistence\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Configurator\Domain\Models\AttributeCollection;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('configurator_attributes', function (Blueprint $table): void {
            $table->foreignIdFor(AttributeCollection::class, 'collection_id')
                ->nullable()
                ->after('step_id')
                ->constrained('configurator_attribute_collections')
                ->nullOnDelete();

            $table->index(['collection_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::table('configurator_attributes', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('collection_id');
        });
    }
};
