<?php

namespace Database\Seeders;

use App\Enums\ServiceStatusType;
use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Truncate Mail Templates
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('services')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        Service::insert([
            [
                'name' => 'Registered People',
                'route_name' => null,
                'status' => ServiceStatusType::ACTIVE,
            ],
            [
                'name' => 'Online Shop',
                'route_name' => 'classifieds',
                'status' => ServiceStatusType::ACTIVE,
            ],
            [
                'name' => 'Webinar',
                'route_name' => null,
                'status' => ServiceStatusType::INACTIVE,
            ],
            [
                'name' => 'Electronic Contracts',
                'route_name' => 'electronic-contracts',
                'status' => ServiceStatusType::ACTIVE,
            ],
            [
                'name' => 'Knowledge',
                'route_name' => 'knowledges',
                'status' => ServiceStatusType::ACTIVE,
            ],
            [
                'name' => 'Document Management',
                'route_name' => 'document',
                'status' => ServiceStatusType::ACTIVE,
            ],
            [
                'name' => 'Advertisement',
                'route_name' => null,
                'status' => ServiceStatusType::INACTIVE,
            ],
            [
                'name' => 'Quotation Issuance',
                'route_name' => 'forms.quotations',
                'status' => ServiceStatusType::ACTIVE,
            ],
            [
                'name' => 'Purchase Order Issuance',
                'route_name' => 'forms.purchase-orders',
                'status' => ServiceStatusType::ACTIVE,
            ],
            [
                'name' => 'Delivery Slip Issuance',
                'route_name' => 'forms.delivery-slips',
                'status' => ServiceStatusType::ACTIVE,
            ],
            [
                'name' => 'Invoice Issuance',
                'route_name' => 'forms.invoices',
                'status' => ServiceStatusType::ACTIVE,
            ],
            [
                'name' => 'Receipt Issuance',
                'route_name' => 'forms.receipts',
                'status' => ServiceStatusType::ACTIVE,
            ],
            [
                'name' => 'Workflow',
                'route_name' => 'workflows',
                'status' => ServiceStatusType::ACTIVE,
            ],
            [
                'name' => 'Network Map',
                'route_name' => null,
                'status' => ServiceStatusType::INACTIVE,
            ],
        ]);
    }
}
