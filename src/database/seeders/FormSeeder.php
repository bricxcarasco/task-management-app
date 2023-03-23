<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Form;
use DB;

class FormSeeder extends Seeder
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
        DB::table('forms')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Insert pre-defined attributes
        Form::insert([
            [//1
                'rio_id' => 1,
                'neo_id' => null,
                'created_rio_id' => 1,
                'supplier_rio_id' => null,
                'supplier_neo_id' => 2,
                'deleter_rio_id' => null,
                'form_no' => 1,
                'title' => 'Quotation 1',
                'type' => 1,
                'price' => 2000.00,
                'zipcode' => '4118',
                'address' => 'Manila',
                'subject' => 'Quota',
                'delivery_address' => 'Manila',
                'payment_terms' => 'card',
                'delivery_date' => '2022-01-30',
                'payment_date' => '2022-01-28',
                'issue_date' => '2022-01-28',
                'expiration_date' => '2022-10-31',
                'receipt_date' => '2022-01-28',
                'deleted_at' => null,
            ],
            [//2
                'rio_id' => 1,
                'neo_id' => null,
                'created_rio_id' => 1,
                'supplier_rio_id' => 1,
                'supplier_neo_id' => null,
                'deleter_rio_id' => null,
                'form_no' => 2,
                'title' => 'Quotation 2',
                'type' => 1,
                'price' => 4000.00,
                'zipcode' => '4118',
                'address' => 'Manila',
                'subject' => 'Quota',
                'delivery_address' => 'Cavite',
                'payment_terms' => 'card',
                'delivery_date' => '2022-01-30',
                'payment_date' => '2022-01-28',
                'issue_date' => '2022-01-29',
                'expiration_date' => '2022-10-30',
                'receipt_date' => '2022-01-28',
                'deleted_at' => null,
            ],
            [//3
                'rio_id' => 3,
                'neo_id' => null,
                'created_rio_id' => 3,
                'supplier_rio_id' => 1,
                'supplier_neo_id' => null,
                'deleter_rio_id' => null,
                'form_no' => 3,
                'title' => 'Quotation 3',
                'type' => 1,
                'price' => 4000.00,
                'zipcode' => '4118',
                'address' => 'Manila',
                'subject' => 'Quota',
                'delivery_address' => 'Laguna',
                'payment_terms' => 'card',
                'delivery_date' => '2022-01-30',
                'payment_date' => '2022-01-28',
                'issue_date' => '2022-01-29',
                'expiration_date' => '2022-10-30',
                'receipt_date' => '2022-01-28',
                'deleted_at' => null,
            ],
            [//4
                'rio_id' => 1,
                'neo_id' => null,
                'created_rio_id' => 1,
                'supplier_rio_id' => 1,
                'supplier_neo_id' => null,
                'deleter_rio_id' => null,
                'form_no' => 4,
                'title' => 'Quotation 4',
                'type' => 1,
                'price' => 4000.00,
                'zipcode' => '4118',
                'address' => 'Manila',
                'subject' => 'Quota',
                'delivery_address' => 'Laguna',
                'payment_terms' => 'card',
                'delivery_date' => '2022-01-30',
                'payment_date' => '2022-01-28',
                'issue_date' => '2022-01-29',
                'expiration_date' => '2022-10-30',
                'receipt_date' => '2022-01-28',
                'deleted_at' => null,
            ],
            [//5
                'rio_id' => 1,
                'neo_id' => null,
                'created_rio_id' => 1,
                'supplier_rio_id' => 1,
                'supplier_neo_id' => null,
                'deleter_rio_id' => null,
                'form_no' => 5,
                'title' => 'Quotation 5',
                'type' => 1,
                'price' => 4000.00,
                'zipcode' => '4118',
                'address' => 'Manila',
                'subject' => 'Quota',
                'delivery_address' => 'Laguna',
                'payment_terms' => 'card',
                'delivery_date' => '2022-01-30',
                'payment_date' => '2022-01-28',
                'issue_date' => '2022-01-29',
                'expiration_date' => '2022-10-30',
                'receipt_date' => '2022-01-28',
                'deleted_at' => null,
            ],
            [//6
                'rio_id' => 1,
                'neo_id' => null,
                'created_rio_id' => 1,
                'supplier_rio_id' => 1,
                'supplier_neo_id' => null,
                'deleter_rio_id' => 3,
                'form_no' => 6,
                'title' => 'Quotation 6',
                'type' => 1,
                'price' => 4000.00,
                'zipcode' => '4118',
                'address' => 'Manila',
                'subject' => 'Quota',
                'delivery_address' => 'Laguna',
                'payment_terms' => 'card',
                'delivery_date' => '2022-01-30',
                'payment_date' => '2022-01-28',
                'issue_date' => '2022-01-29',
                'expiration_date' => '2022-10-30',
                'receipt_date' => '2022-01-28',
                'deleted_at' => now(),
            ],
            [//7
                'rio_id' => 1,
                'neo_id' => null,
                'created_rio_id' => 1,
                'supplier_rio_id' => 1,
                'supplier_neo_id' => null,
                'deleter_rio_id' => 3,
                'form_no' => 7,
                'title' => 'Quotation 7',
                'type' => 1,
                'price' => 4000.00,
                'zipcode' => '4118',
                'address' => 'Manila',
                'subject' => 'Quota',
                'delivery_address' => 'Laguna',
                'payment_terms' => 'card',
                'delivery_date' => '2022-01-30',
                'payment_date' => '2022-01-28',
                'issue_date' => '2022-01-29',
                'expiration_date' => '2022-10-30',
                'receipt_date' => '2022-01-28',
                'deleted_at' => now(),
            ],
            [//8
                'rio_id' => 1,
                'neo_id' => null,
                'created_rio_id' => 1,
                'supplier_rio_id' => 1,
                'supplier_neo_id' => null,
                'deleter_rio_id' => 3,
                'form_no' => 8,
                'title' => 'Quotation 8',
                'type' => 1,
                'price' => 4000.00,
                'zipcode' => '4118',
                'address' => 'Manila',
                'subject' => 'Quota',
                'delivery_address' => 'Laguna',
                'payment_terms' => 'card',
                'delivery_date' => '2022-01-30',
                'payment_date' => '2022-01-28',
                'issue_date' => '2022-01-29',
                'expiration_date' => '2022-10-30',
                'receipt_date' => '2022-01-28',
                'deleted_at' => now(),
            ],
            [//9
                'rio_id' => 1,
                'neo_id' => null,
                'created_rio_id' => 1,
                'supplier_rio_id' => 1,
                'supplier_neo_id' => null,
                'deleter_rio_id' => 3,
                'form_no' => 9,
                'title' => 'Quotation 9',
                'type' => 1,
                'price' => 4000.00,
                'zipcode' => '4118',
                'address' => 'Manila',
                'subject' => 'Quota',
                'delivery_address' => 'Laguna',
                'payment_terms' => 'card',
                'delivery_date' => '2022-01-30',
                'payment_date' => '2022-01-28',
                'issue_date' => '2022-01-29',
                'expiration_date' => '2022-10-30',
                'receipt_date' => '2022-01-28',
                'deleted_at' => now(),
            ],
            [//10
                'rio_id' => 1,
                'neo_id' => null,
                'created_rio_id' => 1,
                'supplier_rio_id' => 1,
                'supplier_neo_id' => null,
                'deleter_rio_id' => 3,
                'form_no' => 10,
                'title' => 'Quotation 10',
                'type' => 1,
                'price' => 4000.00,
                'zipcode' => '4118',
                'address' => 'Manila',
                'subject' => 'Quota',
                'delivery_address' => 'Laguna',
                'payment_terms' => 'card',
                'delivery_date' => '2022-01-30',
                'payment_date' => '2022-01-28',
                'issue_date' => '2022-01-29',
                'expiration_date' => '2022-10-30',
                'receipt_date' => '2022-01-28',
                'deleted_at' => now(),
            ],
            [//11
                'rio_id' => 1,
                'neo_id' => null,
                'created_rio_id' => 1,
                'supplier_rio_id' => 1,
                'supplier_neo_id' => null,
                'deleter_rio_id' => 3,
                'form_no' => 11,
                'title' => 'Quotation 11',
                'type' => 1,
                'price' => 4000.00,
                'zipcode' => '4118',
                'address' => 'Manila',
                'subject' => 'Quota',
                'delivery_address' => 'Laguna',
                'payment_terms' => 'card',
                'delivery_date' => '2022-01-30',
                'payment_date' => '2022-01-28',
                'issue_date' => '2022-01-29',
                'expiration_date' => '2022-10-30',
                'receipt_date' => '2022-01-28',
                'deleted_at' => now(),
            ],
            [//12
                'rio_id' => 1,
                'neo_id' => null,
                'created_rio_id' => 1,
                'supplier_rio_id' => 1,
                'supplier_neo_id' => null,
                'deleter_rio_id' => 3,
                'form_no' => 12,
                'title' => 'Quotation 12',
                'type' => 1,
                'price' => 4000.00,
                'zipcode' => '4118',
                'address' => 'Manila',
                'subject' => 'Quota',
                'delivery_address' => 'Laguna',
                'payment_terms' => 'card',
                'delivery_date' => '2022-01-30',
                'payment_date' => '2022-01-28',
                'issue_date' => '2022-01-29',
                'expiration_date' => '2022-10-30',
                'receipt_date' => '2022-01-28',
                'deleted_at' => now(),
            ],
            [//13
                'rio_id' => 1,
                'neo_id' => null,
                'created_rio_id' => 1,
                'supplier_rio_id' => 1,
                'supplier_neo_id' => null,
                'deleter_rio_id' => 3,
                'form_no' => 13,
                'title' => 'Quotation 13',
                'type' => 1,
                'price' => 4000.00,
                'zipcode' => '4118',
                'address' => 'Manila',
                'subject' => 'Quota',
                'delivery_address' => 'Laguna',
                'payment_terms' => 'card',
                'delivery_date' => '2022-01-30',
                'payment_date' => '2022-01-28',
                'issue_date' => '2022-01-29',
                'expiration_date' => '2022-10-30',
                'receipt_date' => '2022-01-28',
                'deleted_at' => now(),
            ],
            [//14
                'rio_id' => 1,
                'neo_id' => null,
                'created_rio_id' => 1,
                'supplier_rio_id' => 1,
                'supplier_neo_id' => null,
                'deleter_rio_id' => 3,
                'form_no' => 14,
                'title' => 'Quotation 14',
                'type' => 1,
                'price' => 4000.00,
                'zipcode' => '4118',
                'address' => 'Manila',
                'subject' => 'Quota',
                'delivery_address' => 'Laguna',
                'payment_terms' => 'card',
                'delivery_date' => '2022-01-30',
                'payment_date' => '2022-01-28',
                'issue_date' => '2022-01-29',
                'expiration_date' => '2022-10-30',
                'receipt_date' => '2022-01-28',
                'deleted_at' => now(),
            ],
            [//15
                'rio_id' => 1,
                'neo_id' => null,
                'created_rio_id' => 1,
                'supplier_rio_id' => 1,
                'supplier_neo_id' => null,
                'deleter_rio_id' => 3,
                'form_no' => 15,
                'title' => 'Quotation 15',
                'type' => 1,
                'price' => 4000.00,
                'zipcode' => '4118',
                'address' => 'Manila',
                'subject' => 'Quota',
                'delivery_address' => 'Laguna',
                'payment_terms' => 'card',
                'delivery_date' => '2022-01-30',
                'payment_date' => '2022-01-28',
                'issue_date' => '2022-01-29',
                'expiration_date' => '2022-10-30',
                'receipt_date' => '2022-01-28',
                'deleted_at' => now(),
            ],
            [//16
                'rio_id' => 1,
                'neo_id' => null,
                'created_rio_id' => 1,
                'supplier_rio_id' => 1,
                'supplier_neo_id' => null,
                'deleter_rio_id' => 3,
                'form_no' => 16,
                'title' => 'Quotation 16',
                'type' => 1,
                'price' => 4000.00,
                'zipcode' => '4118',
                'address' => 'Manila',
                'subject' => 'Quota',
                'delivery_address' => 'Laguna',
                'payment_terms' => 'card',
                'delivery_date' => '2022-01-30',
                'payment_date' => '2022-01-28',
                'issue_date' => '2022-01-29',
                'expiration_date' => '2022-10-30',
                'receipt_date' => '2022-01-28',
                'deleted_at' => now(),
            ],
            [//17
                'rio_id' => 1,
                'neo_id' => null,
                'created_rio_id' => 1,
                'supplier_rio_id' => 1,
                'supplier_neo_id' => null,
                'deleter_rio_id' => 3,
                'form_no' => 17,
                'title' => 'Quotation 17',
                'type' => 1,
                'price' => 4000.00,
                'zipcode' => '4118',
                'address' => 'Manila',
                'subject' => 'Quota',
                'delivery_address' => 'Laguna',
                'payment_terms' => 'card',
                'delivery_date' => '2022-01-30',
                'payment_date' => '2022-01-28',
                'issue_date' => '2022-01-29',
                'expiration_date' => '2022-10-30',
                'receipt_date' => '2022-01-28',
                'deleted_at' => now(),
            ],
            [//18
                'rio_id' => 1,
                'neo_id' => null,
                'created_rio_id' => 1,
                'supplier_rio_id' => 1,
                'supplier_neo_id' => null,
                'deleter_rio_id' => 3,
                'form_no' => 18,
                'title' => 'Quotation 18',
                'type' => 1,
                'price' => 4000.00,
                'zipcode' => '4118',
                'address' => 'Manila',
                'subject' => 'Quota',
                'delivery_address' => 'Laguna',
                'payment_terms' => 'card',
                'delivery_date' => '2022-01-30',
                'payment_date' => '2022-01-28',
                'issue_date' => '2022-01-29',
                'expiration_date' => '2022-10-30',
                'receipt_date' => '2022-01-28',
                'deleted_at' => now(),
            ],
            [//19
                'rio_id' => 1,
                'neo_id' => null,
                'created_rio_id' => 1,
                'supplier_rio_id' => 1,
                'supplier_neo_id' => null,
                'deleter_rio_id' => 3,
                'form_no' => 19,
                'title' => 'Quotation 19',
                'type' => 1,
                'price' => 4000.00,
                'zipcode' => '4118',
                'address' => 'Manila',
                'subject' => 'Quota',
                'delivery_address' => 'Laguna',
                'payment_terms' => 'card',
                'delivery_date' => '2022-01-30',
                'payment_date' => '2022-01-28',
                'issue_date' => '2022-01-29',
                'expiration_date' => '2022-10-30',
                'receipt_date' => '2022-01-28',
                'deleted_at' => now(),
            ],
            [//20
                'rio_id' => 1,
                'neo_id' => null,
                'created_rio_id' => 1,
                'supplier_rio_id' => 1,
                'supplier_neo_id' => null,
                'deleter_rio_id' => 3,
                'form_no' => 20,
                'title' => 'Purchase Order 20',
                'type' => 2,
                'price' => 4000.00,
                'zipcode' => '4118',
                'address' => 'Manila',
                'subject' => 'Quota',
                'delivery_address' => 'Laguna',
                'payment_terms' => 'card',
                'delivery_date' => '2022-01-30',
                'payment_date' => '2022-01-28',
                'issue_date' => '2022-01-29',
                'expiration_date' => '2022-10-30',
                'receipt_date' => '2022-01-28',
                'deleted_at' => now(),
            ],
            [//21
                'rio_id' => 1,
                'neo_id' => null,
                'created_rio_id' => 1,
                'supplier_rio_id' => 1,
                'supplier_neo_id' => null,
                'deleter_rio_id' => 3,
                'form_no' => 21,
                'title' => 'Delivery Slip 21',
                'type' => 3,
                'price' => 4000.00,
                'zipcode' => '4118',
                'address' => 'Manila',
                'subject' => 'Quota',
                'delivery_address' => 'Laguna',
                'payment_terms' => 'card',
                'delivery_date' => '2022-01-30',
                'payment_date' => '2022-01-28',
                'issue_date' => '2022-01-29',
                'expiration_date' => '2022-10-30',
                'receipt_date' => '2022-01-28',
                'deleted_at' => now(),
            ],
            [//22
                'rio_id' => 1,
                'neo_id' => null,
                'created_rio_id' => 1,
                'supplier_rio_id' => 1,
                'supplier_neo_id' => null,
                'deleter_rio_id' => 3,
                'form_no' => 22,
                'title' => 'Invoice 22',
                'type' => 4,
                'price' => 4000.00,
                'zipcode' => '4118',
                'address' => 'Manila',
                'subject' => 'Quota',
                'delivery_address' => 'Laguna',
                'payment_terms' => 'card',
                'delivery_date' => '2022-01-30',
                'payment_date' => '2022-01-28',
                'issue_date' => '2022-01-29',
                'expiration_date' => '2022-10-30',
                'receipt_date' => '2022-01-28',
                'deleted_at' => now(),
            ],
            [//23
                'rio_id' => 1,
                'neo_id' => null,
                'created_rio_id' => 1,
                'supplier_rio_id' => 1,
                'supplier_neo_id' => null,
                'deleter_rio_id' => 3,
                'form_no' => 23,
                'title' => 'Receipt 23',
                'type' => 5,
                'price' => 4000.00,
                'zipcode' => '4118',
                'address' => 'Manila',
                'subject' => 'Quota',
                'delivery_address' => 'Laguna',
                'payment_terms' => 'card',
                'delivery_date' => '2022-01-30',
                'payment_date' => '2022-01-28',
                'issue_date' => '2022-01-29',
                'expiration_date' => '2022-10-30',
                'receipt_date' => '2022-01-28',
                'deleted_at' => now(),
            ],
            [//24
                'rio_id' => 1,
                'neo_id' => null,
                'created_rio_id' => 1,
                'supplier_rio_id' => 1,
                'supplier_neo_id' => null,
                'deleter_rio_id' => 3,
                'form_no' => 24,
                'title' => 'Quotation 24',
                'type' => 1,
                'price' => 4000.00,
                'zipcode' => '4118',
                'address' => 'Manila',
                'subject' => 'Quota',
                'delivery_address' => 'Laguna',
                'payment_terms' => 'card',
                'delivery_date' => '2022-01-30',
                'payment_date' => '2022-01-28',
                'issue_date' => '2022-01-29',
                'expiration_date' => '2022-10-30',
                'receipt_date' => '2022-01-28',
                'deleted_at' => now(),
            ],
            [//25
                'rio_id' => 1,
                'neo_id' => null,
                'created_rio_id' => 1,
                'supplier_rio_id' => 1,
                'supplier_neo_id' => null,
                'deleter_rio_id' => 3,
                'form_no' => 25,
                'title' => 'Quotation 25',
                'type' => 1,
                'price' => 4000.00,
                'zipcode' => '4118',
                'address' => 'Manila',
                'subject' => 'Quota',
                'delivery_address' => 'Laguna',
                'payment_terms' => 'card',
                'delivery_date' => '2022-01-30',
                'payment_date' => '2022-01-28',
                'issue_date' => '2022-01-29',
                'expiration_date' => '2022-10-30',
                'receipt_date' => '2022-01-28',
                'deleted_at' => now(),
            ],
            [//26
                'rio_id' => 1,
                'neo_id' => null,
                'created_rio_id' => 1,
                'supplier_rio_id' => 1,
                'supplier_neo_id' => null,
                'deleter_rio_id' => 3,
                'form_no' => 26,
                'title' => 'Quotation 26',
                'type' => 1,
                'price' => 4000.00,
                'zipcode' => '4118',
                'address' => 'Manila',
                'subject' => 'Quota',
                'delivery_address' => 'Laguna',
                'payment_terms' => 'card',
                'delivery_date' => '2022-01-30',
                'payment_date' => '2022-01-28',
                'issue_date' => '2022-01-29',
                'expiration_date' => '2022-10-30',
                'receipt_date' => '2022-01-28',
                'deleted_at' => now(),
            ],
            [//27
                'rio_id' => 1,
                'neo_id' => null,
                'created_rio_id' => 1,
                'supplier_rio_id' => 1,
                'supplier_neo_id' => null,
                'deleter_rio_id' => 3,
                'form_no' => 27,
                'title' => 'Quotation 27',
                'type' => 1,
                'price' => 4000.00,
                'zipcode' => '4118',
                'address' => 'Manila',
                'subject' => 'Quota',
                'delivery_address' => 'Laguna',
                'payment_terms' => 'card',
                'delivery_date' => '2022-01-30',
                'payment_date' => '2022-01-28',
                'issue_date' => '2022-01-29',
                'expiration_date' => '2022-10-30',
                'receipt_date' => '2022-01-28',
                'deleted_at' => now(),
            ],
            [//28
                'rio_id' => 1,
                'neo_id' => null,
                'created_rio_id' => 1,
                'supplier_rio_id' => 1,
                'supplier_neo_id' => null,
                'deleter_rio_id' => 3,
                'form_no' => 28,
                'title' => 'Quotation 28',
                'type' => 1,
                'price' => 4000.00,
                'zipcode' => '4118',
                'address' => 'Manila',
                'subject' => 'Quota',
                'delivery_address' => 'Laguna',
                'payment_terms' => 'card',
                'delivery_date' => '2022-01-30',
                'payment_date' => '2022-01-28',
                'issue_date' => '2022-01-29',
                'expiration_date' => '2022-10-30',
                'receipt_date' => '2022-01-28',
                'deleted_at' => now(),
            ],
            [//29
                'rio_id' => 1,
                'neo_id' => null,
                'created_rio_id' => 1,
                'supplier_rio_id' => 1,
                'supplier_neo_id' => null,
                'deleter_rio_id' => 3,
                'form_no' => 29,
                'title' => 'Quotation 29',
                'type' => 1,
                'price' => 4000.00,
                'zipcode' => '4118',
                'address' => 'Manila',
                'subject' => 'Quota',
                'delivery_address' => 'Laguna',
                'payment_terms' => 'card',
                'delivery_date' => '2022-01-30',
                'payment_date' => '2022-01-28',
                'issue_date' => '2022-01-29',
                'expiration_date' => '2022-10-30',
                'receipt_date' => '2022-01-28',
                'deleted_at' => now(),
            ],
            [//30
                'rio_id' => 1,
                'neo_id' => null,
                'created_rio_id' => 1,
                'supplier_rio_id' => 1,
                'supplier_neo_id' => null,
                'deleter_rio_id' => 3,
                'form_no' => 30,
                'title' => 'Quotation 30',
                'type' => 1,
                'price' => 4000.00,
                'zipcode' => '4118',
                'address' => 'Manila',
                'subject' => 'Quota',
                'delivery_address' => 'Laguna',
                'payment_terms' => 'card',
                'delivery_date' => '2022-01-30',
                'payment_date' => '2022-01-28',
                'issue_date' => '2022-01-29',
                'expiration_date' => '2022-10-30',
                'receipt_date' => '2022-01-28',
                'deleted_at' => now(),
            ],
            [//31
                'rio_id' => 1,
                'neo_id' => null,
                'created_rio_id' => 1,
                'supplier_rio_id' => 1,
                'supplier_neo_id' => null,
                'deleter_rio_id' => 3,
                'form_no' => 31,
                'title' => 'Quotation 31',
                'type' => 1,
                'price' => 4000.00,
                'zipcode' => '4118',
                'address' => 'Manila',
                'subject' => 'Quota',
                'delivery_address' => 'Laguna',
                'payment_terms' => 'card',
                'delivery_date' => '2022-01-30',
                'payment_date' => '2022-01-28',
                'issue_date' => '2022-01-29',
                'expiration_date' => '2022-10-30',
                'receipt_date' => '2022-01-28',
                'deleted_at' => now(),
            ],
        ]);
    }
}