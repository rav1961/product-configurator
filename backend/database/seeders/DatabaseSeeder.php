<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Configurator\Infrastructure\Persistence\Seeders\DemoConfiguratorSeeder;
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
            DemoConfiguratorSeeder::class,
        ]);
    }
}
