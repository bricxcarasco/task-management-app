<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DocumentAccess;
use DB;

class DocumentAccessSeeder extends Seeder
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
        DB::table('document_accesses')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Insert pre-defined attributes
        DocumentAccess::insert([
            [//1 rio user 1's document_1.jpg is shared to RIO user with rio_id 2
                'document_id' => 1,
                'neo_id' => null,
                'rio_id' => 2,
                'neo_group_id' => null,
            ],
            [//2 document data with folder name Folder_1 is shared to RIO user with rio_id 2
                'document_id' => 4,
                'neo_id' => null,
                'rio_id' => 2,
                'neo_group_id' => null,
            ],
            [//3 rio user 2's Folder2_2 is shared to RIO user with rio_id 1
                'document_id' => 16,
                'neo_id' => null,
                'rio_id' => 1,
                'neo_group_id' => null,
            ],
            [//4 rio user 2's Folder2_2 is shared to NEO org with neo_id 1
                'document_id' => 16,
                'neo_id' => 1,
                'rio_id' => null,
                'neo_group_id' => null,
            ],
            [//5 rio user 1's document_1.jpg is shared to NEO org with neo_id 1
                'document_id' => 1,
                'neo_id' => 1,
                'rio_id' => null,
                'neo_group_id' => null,
            ],
            [//5 neo org 3's neo_document2_3.jpg is shared to RIO user with rio_id 2
                'document_id' => 25,
                'neo_id' => null,
                'rio_id' => 2,
                'neo_group_id' => null,
            ],
            [//5 neo org 3's neo_document2_3.jpg is shared to NEO org with neo_id 1
                'document_id' => 25,
                'neo_id' => 1,
                'rio_id' => null,
                'neo_group_id' => null,
            ],
            [//6 neo org 3's Folder1 2nd is shared to NEO org with rio_id 2
                'document_id' => 26,
                'neo_id' => null,
                'rio_id' => 2,
                'neo_group_id' => null,
            ],
        ]);
    }
}
