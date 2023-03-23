<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            MailTemplateSeeder::class,
            BusinessCategorySeeder::class,
            ExpertAttributeSeeder::class,
            StorageTypeSeeder::class,
            ClassifiedSaleCategorySeeder::class,
            PlanSeeder::class,
            PlanServiceSeeder::class,
            ServiceSeeder::class,
        ]);
    }
}
