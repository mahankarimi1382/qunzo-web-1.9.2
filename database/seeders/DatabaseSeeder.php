<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // PermissionSeeder::class,
            // AdminSeeder::class,
            // UserSeeder::class,
            // KycSeeder::class,
            // TemplateSeeder::class,
            // LandingSectionSeeder::class,
            // CronJobSeeder::class,
            // SettingSeeder::class,
            // UserNavigationSeeder::class,
        ]);
    }
}
