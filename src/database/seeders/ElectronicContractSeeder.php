<?php

namespace Database\Seeders;

use App\Enums\ElectronicContract\ElectronicContractStatuses;
use App\Models\ElectronicContract;
use DB;
use Illuminate\Database\Seeder;

class ElectronicContractSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Truncate Expert Attribute
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('electronic_contracts')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Insert pre-defined attributes
        ElectronicContract::insert([
            [//1
                'rio_id' => 1,
                'neo_id' => null,
                'created_rio_id' => 1,
                'contract_document_id' => 2,
                'dossier_id' => 'b041a287-bc92-4469-801e-ae1a39c08f6e',
                'recipient_rio_id' => 2,
                'recipient_neo_id' => null,
                'recipient_email' => 'rio2email@gmail.com',
                'status' => ElectronicContractStatuses::CREATED,
            ],
            [//2
                'rio_id' => null,
                'neo_id' => 2,
                'created_rio_id' => 1,
                'contract_document_id' => 2,
                'dossier_id' => 'b659c273-954e-43cf-893a-0f74a7f87153',
                'recipient_rio_id' => 3,
                'recipient_neo_id' => null,
                'recipient_email' => 'rio3email@gmail.com',
                'status' => ElectronicContractStatuses::CREATED,
            ],
            [//3
                'rio_id' => null,
                'neo_id' => 3,
                'created_rio_id' => 1,
                'contract_document_id' => 2,
                'dossier_id' => '77e79832-1708-4a49-9528-3536ba4057d7',
                'recipient_rio_id' => 2,
                'recipient_neo_id' => null,
                'recipient_email' => 'rio2email@gmail.com',
                'status' => ElectronicContractStatuses::CREATED,
            ],
            [//3
                'rio_id' => 1,
                'neo_id' => null,
                'created_rio_id' => 1,
                'contract_document_id' => 2,
                'dossier_id' => '22b33b45-bde6-45f4-a45a-ec1fa53ffffa',
                'recipient_rio_id' => null,
                'recipient_neo_id' => 5,
                'recipient_email' => 'neo5email@gmail.com',
                'status' => ElectronicContractStatuses::CREATED,
            ],
        ]);
    }
}
