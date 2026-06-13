<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement('CREATE EXTENSION IF NOT EXISTS pg_trgm');

        DB::statement("
            CREATE INDEX IF NOT EXISTS catalog_products_search_fts_idx
            ON catalog_products
            USING GIN (to_tsvector('simple', coalesce(name, '') || ' ' || coalesce(sku, '')))
        ");

        DB::statement('
            CREATE INDEX IF NOT EXISTS catalog_products_name_trgm_idx
            ON catalog_products
            USING GIN (lower(name) gin_trgm_ops)
        ');

        DB::statement("
            CREATE INDEX IF NOT EXISTS catalog_products_sku_trgm_idx
            ON catalog_products
            USING GIN (lower(coalesce(sku, '')) gin_trgm_ops)
        ");

        DB::statement('
            CREATE INDEX IF NOT EXISTS catalog_products_lower_sku_btree_idx
            ON catalog_products (lower(sku))
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement('DROP INDEX IF EXISTS catalog_products_lower_sku_btree_idx');
        DB::statement('DROP INDEX IF EXISTS catalog_products_sku_trgm_idx');
        DB::statement('DROP INDEX IF EXISTS catalog_products_name_trgm_idx');
        DB::statement('DROP INDEX IF EXISTS catalog_products_search_fts_idx');
    }
};
