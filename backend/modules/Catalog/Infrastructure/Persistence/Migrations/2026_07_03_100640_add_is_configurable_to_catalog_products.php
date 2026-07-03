<?php

declare(strict_types=1);

namespace Modules\Catalog\Infrastructure\Persistence\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('catalog_products', function (Blueprint $table) {
            $table->boolean('is_configurable')->default(false)->after('status');

            $table->index(['status', 'is_configurable', 'position']);
        });
    }

    public function down(): void
    {
        Schema::table('catalog_products', function (Blueprint $table) {
            $table->dropIndex(['status', 'is_configurable', 'position']);
            $table->dropColumn('is_configurable');
        });
    }
};
