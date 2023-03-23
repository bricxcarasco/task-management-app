<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StorageType;
use DB;

class StorageTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Truncate Storage Type
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('storage_types')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Insert pre-defined attributes
        StorageType::insert([
            [
                'storage_name' => 'HERO',
            ],
            [
                'storage_name' => 'Google Drive',
            ],
            [
                'storage_name' => 'DropBox',
            ],
        ]);
    }
}
