<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Catalog\Infrastructure\Persistence\Seeders\DemoCatalogSeeder;
use Modules\Users\Infrastructure\Persistence\Seeders\DemoUsersSeeder;
use Modules\Users\Infrastructure\Persistence\Seeders\RoleSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            DemoUsersSeeder::class,
            DemoCatalogSeeder::class,
        ]);
    }
}
