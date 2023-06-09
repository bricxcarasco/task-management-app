<?php

namespace Database\Seeders;

use App\Enums\PlanServiceType;
use App\Models\PlanService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Str;

class PlanServiceSeeder extends Seeder
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
        DB::table('plan_services')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $seederData = [];

        /**
         * RIO - Free Plan
         */
        $seederData = array_merge($seederData, [
            [
                'plan_id' => 1,
                'service_id' => 5,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 1,
                'price' => null,
                'unit' => null,
                'description' => null,
            ],
            [
                'plan_id' => 1,
                'service_id' => 6,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 5,
                'price' => null,
                'unit' => 'GB',
                'description' => null,
            ],
            [
                'plan_id' => 1,
                'service_id' => 7,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 1,
                'price' => null,
                'unit' => 'cases',
                'description' => null,
            ],
        ]);

        /**
         * RIO - Light Plan
         */
        $seederData = array_merge($seederData, [
            [
                'plan_id' => 2,
                'service_id' => 4,
                'type' => PlanServiceType::OPTION,
                'stripe_price_id' => Str::random(),
                'value' => 1,
                'price' => 1000,
                'unit' => 'times',
                'description' => null,
            ],
            [
                'plan_id' => 2,
                'service_id' => 5,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 1,
                'price' => null,
                'unit' => null,
                'description' => null,
            ],
            [
                'plan_id' => 2,
                'service_id' => 6,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 7,
                'price' => null,
                'unit' => 'GB',
                'description' => null,
            ],
            [
                'plan_id' => 2,
                'service_id' => 7,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 5,
                'price' => null,
                'unit' => 'cases',
                'description' => null,
            ],
            [
                'plan_id' => 2,
                'service_id' => 14,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 1,
                'price' => null,
                'unit' => 'time',
                'description' => null,
            ],
            [
                'plan_id' => 2,
                'service_id' => 3,
                'type' => PlanServiceType::OPTION,
                'stripe_price_id' => Str::random(),
                'value' => 3,
                'price' => 5000,
                'unit' => 'hours',
                'description' => 'Max 50 participants',
            ],
        ]);

        /**
         * RIO - Standard Plan
         */
        $seederData = array_merge($seederData, [
            [
                'plan_id' => 3,
                'service_id' => 2,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 1,
                'price' => null,
                'unit' => null,
                'description' => null,
            ],
            [
                'plan_id' => 3,
                'service_id' => 3,
                'type' => PlanServiceType::OPTION,
                'stripe_price_id' => Str::random(),
                'value' => 3,
                'price' => 5000,
                'unit' => 'hours',
                'description' => 'Max 50 participants',
            ],
            [
                'plan_id' => 3,
                'service_id' => 4,
                'type' => PlanServiceType::OPTION,
                'stripe_price_id' => Str::random(),
                'value' => 1,
                'price' => 1000,
                'unit' => 'times',
                'description' => null,
            ],
            [
                'plan_id' => 3,
                'service_id' => 5,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 1,
                'price' => null,
                'unit' => null,
                'description' => null,
            ],
            [
                'plan_id' => 3,
                'service_id' => 6,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 20,
                'price' => null,
                'unit' => 'GB',
                'description' => null,
            ],
            [
                'plan_id' => 3,
                'service_id' => 7,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 10,
                'price' => null,
                'unit' => 'cases',
                'description' => null,
            ],
            [
                'plan_id' => 3,
                'service_id' => 8,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 1,
                'price' => null,
                'unit' => null,
                'description' => null,
            ],
            [
                'plan_id' => 3,
                'service_id' => 9,
                'type' => PlanServiceType::OPTION,
                'stripe_price_id' => Str::random(),
                'value' => 1,
                'price' => 500,
                'unit' => null,
                'description' => null,
            ],
            [
                'plan_id' => 3,
                'service_id' => 10,
                'type' => PlanServiceType::OPTION,
                'stripe_price_id' => Str::random(),
                'value' => 1,
                'price' => 500,
                'unit' => null,
                'description' => null,
            ],
            [
                'plan_id' => 3,
                'service_id' => 11,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 1,
                'price' => null,
                'unit' => null,
                'description' => null,
            ],
            [
                'plan_id' => 3,
                'service_id' => 12,
                'type' => PlanServiceType::OPTION,
                'stripe_price_id' => Str::random(),
                'value' => 1,
                'price' => 1000,
                'unit' => null,
                'description' => null,
            ],
            [
                'plan_id' => 3,
                'service_id' => 14,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 5,
                'price' => null,
                'unit' => 'times',
                'description' => null,
            ],
        ]);

        /**
         * RIO - Premium Plan
         */
        $seederData = array_merge($seederData, [
            [
                'plan_id' => 4,
                'service_id' => 2,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 1,
                'price' => null,
                'unit' => null,
                'description' => null,
            ],
            [
                'plan_id' => 4,
                'service_id' => 3,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 3,
                'price' => null,
                'unit' => 'hours',
                'description' => 'Max 50 participants',
            ],
            [
                'plan_id' => 4,
                'service_id' => 3,
                'type' => PlanServiceType::OPTION,
                'stripe_price_id' => Str::random(),
                'value' => 3,
                'price' => null,
                'unit' => 'hours',
                'description' => 'Max 50 participants',
            ],
            [
                'plan_id' => 4,
                'service_id' => 4,
                'type' => PlanServiceType::OPTION,
                'stripe_price_id' => Str::random(),
                'value' => 1,
                'price' => 1000,
                'unit' => 'times',
                'description' => null,
            ],
            [
                'plan_id' => 4,
                'service_id' => 5,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 1,
                'price' => null,
                'unit' => null,
                'description' => null,
            ],
            [
                'plan_id' => 4,
                'service_id' => 6,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 50,
                'price' => null,
                'unit' => 'GB',
                'description' => null,
            ],
            [
                'plan_id' => 4,
                'service_id' => 7,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 20,
                'price' => null,
                'unit' => 'cases',
                'description' => null,
            ],
            [
                'plan_id' => 4,
                'service_id' => 8,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 1,
                'price' => null,
                'unit' => null,
                'description' => null,
            ],
            [
                'plan_id' => 4,
                'service_id' => 9,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 1,
                'price' => null,
                'unit' => null,
                'description' => null,
            ],
            [
                'plan_id' => 4,
                'service_id' => 10,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 1,
                'price' => null,
                'unit' => null,
                'description' => null,
            ],
            [
                'plan_id' => 4,
                'service_id' => 11,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 1,
                'price' => null,
                'unit' => null,
                'description' => null,
            ],
            [
                'plan_id' => 4,
                'service_id' => 12,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 1,
                'price' => null,
                'unit' => null,
                'description' => null,
            ],
            [
                'plan_id' => 4,
                'service_id' => 14,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 10,
                'price' => null,
                'unit' => 'times',
                'description' => null,
            ],
        ]);

        /**
         * NEO - Free Plan
         */
        $seederData = array_merge($seederData, [
            [
                'plan_id' => 5,
                'service_id' => 1,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 5,
                'price' => null,
                'unit' => 'person',
                'description' => null,
            ],
            [
                'plan_id' => 5,
                'service_id' => 1,
                'type' => PlanServiceType::OPTION,
                'stripe_price_id' => Str::random(),
                'value' => 1,
                'price' => 500,
                'unit' => 'person',
                'description' => null,
            ],
            [
                'plan_id' => 5,
                'service_id' => 2,
                'type' => PlanServiceType::OPTION,
                'stripe_price_id' => Str::random(),
                'value' => 1,
                'price' => 5000,
                'unit' => null,
                'description' => null,
            ],
            [
                'plan_id' => 5,
                'service_id' => 3,
                'type' => PlanServiceType::OPTION,
                'stripe_price_id' => Str::random(),
                'value' => 3,
                'price' => 5000,
                'unit' => 'hours',
                'description' => 'Max 50 persons',
            ],
            [
                'plan_id' => 5,
                'service_id' => 4,
                'type' => PlanServiceType::OPTION,
                'stripe_price_id' => Str::random(),
                'value' => 1,
                'price' => 1000,
                'unit' => 'times',
                'description' => null,
            ],
            [
                'plan_id' => 5,
                'service_id' => 6,
                'type' => PlanServiceType::OPTION,
                'stripe_price_id' => Str::random(),
                'value' => 10,
                'price' => 700,
                'unit' => 'GB',
                'description' => null,
            ],
            [
                'plan_id' => 5,
                'service_id' => 7,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 1,
                'price' => null,
                'unit' => 'case',
                'description' => null,
            ],
            [
                'plan_id' => 5,
                'service_id' => 8,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 1,
                'price' => null,
                'unit' => null,
                'description' => null,
            ],
            [
                'plan_id' => 5,
                'service_id' => 9,
                'type' => PlanServiceType::OPTION,
                'stripe_price_id' => Str::random(),
                'value' => 1,
                'price' => 500,
                'unit' => null,
                'description' => null,
            ],
            [
                'plan_id' => 5,
                'service_id' => 10,
                'type' => PlanServiceType::OPTION,
                'stripe_price_id' => Str::random(),
                'value' => 1,
                'price' => 500,
                'unit' => null,
                'description' => null,
            ],
            [
                'plan_id' => 5,
                'service_id' => 11,
                'type' => PlanServiceType::OPTION,
                'stripe_price_id' => Str::random(),
                'value' => 1,
                'price' => 500,
                'unit' => null,
                'description' => null,
            ],
            [
                'plan_id' => 5,
                'service_id' => 12,
                'type' => PlanServiceType::OPTION,
                'stripe_price_id' => Str::random(),
                'value' => 1,
                'price' => 1000,
                'unit' => null,
                'description' => null,
            ],
            [
                'plan_id' => 5,
                'service_id' => 13,
                'type' => PlanServiceType::OPTION,
                'stripe_price_id' => Str::random(),
                'value' => 1,
                'price' => 1000,
                'unit' => null,
                'description' => null,
            ],
        ]);

        /**
         * NEO - Light Plan
         */
        $seederData = array_merge($seederData, [
            [
                'plan_id' => 6,
                'service_id' => 1,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 50,
                'price' => null,
                'unit' => 'persons',
                'description' => 'Max 500 persons',
            ],
            [
                'plan_id' => 6,
                'service_id' => 1,
                'type' => PlanServiceType::OPTION,
                'stripe_price_id' => Str::random(),
                'value' => 1,
                'price' => 500,
                'unit' => 'person',
                'description' => null,
            ],
            [
                'plan_id' => 6,
                'service_id' => 2,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 1,
                'price' => null,
                'unit' => null,
                'description' => null,
            ],
            [
                'plan_id' => 6,
                'service_id' => 3,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 6,
                'price' => null,
                'unit' => 'hours',
                'description' => 'Max 100 persons',
            ],
            [
                'plan_id' => 6,
                'service_id' => 3,
                'type' => PlanServiceType::OPTION,
                'stripe_price_id' => Str::random(),
                'value' => 3,
                'price' => 5000,
                'unit' => 'hours',
                'description' => 'Max 50 persons',
            ],
            [
                'plan_id' => 6,
                'service_id' => 4,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 1,
                'price' => null,
                'unit' => 'times',
                'description' => null,
            ],
            [
                'plan_id' => 6,
                'service_id' => 4,
                'type' => PlanServiceType::OPTION,
                'stripe_price_id' => Str::random(),
                'value' => 1,
                'price' => 1000,
                'unit' => 'times',
                'description' => null,
            ],
            [
                'plan_id' => 6,
                'service_id' => 5,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 1,
                'price' => null,
                'unit' => null,
                'description' => null,
            ],
            [
                'plan_id' => 6,
                'service_id' => 6,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 50,
                'price' => null,
                'unit' => 'GB',
                'description' => null,
            ],
            [
                'plan_id' => 6,
                'service_id' => 6,
                'type' => PlanServiceType::OPTION,
                'stripe_price_id' => Str::random(),
                'value' => 10,
                'price' => 700,
                'unit' => 'GB',
                'description' => null,
            ],
            [
                'plan_id' => 6,
                'service_id' => 7,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 5,
                'price' => null,
                'unit' => 'cases',
                'description' => null,
            ],
            [
                'plan_id' => 6,
                'service_id' => 8,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 1,
                'price' => null,
                'unit' => null,
                'description' => null,
            ],
            [
                'plan_id' => 6,
                'service_id' => 9,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 1,
                'price' => null,
                'unit' => null,
                'description' => null,
            ],
            [
                'plan_id' => 6,
                'service_id' => 10,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 1,
                'price' => null,
                'unit' => null,
                'description' => null,
            ],
            [
                'plan_id' => 6,
                'service_id' => 11,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 1,
                'price' => null,
                'unit' => null,
                'description' => null,
            ],
            [
                'plan_id' => 6,
                'service_id' => 12,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 1,
                'price' => null,
                'unit' => null,
                'description' => null,
            ],
            [
                'plan_id' => 6,
                'service_id' => 13,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 1,
                'price' => null,
                'unit' => null,
                'description' => null,
            ],
            [
                'plan_id' => 6,
                'service_id' => 14,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 1,
                'price' => null,
                'unit' => 'times',
                'description' => null,
            ],
        ]);

        /**
         * NEO - Standard Plan
         */
        $seederData = array_merge($seederData, [
            [
                'plan_id' => 7,
                'service_id' => 1,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 100,
                'price' => null,
                'unit' => 'persons',
                'description' => 'Max 500 persons',
            ],
            [
                'plan_id' => 7,
                'service_id' => 1,
                'type' => PlanServiceType::OPTION,
                'stripe_price_id' => Str::random(),
                'value' => 1,
                'price' => 500,
                'unit' => 'person',
                'description' => null,
            ],
            [
                'plan_id' => 7,
                'service_id' => 2,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 1,
                'price' => null,
                'unit' => null,
                'description' => null,
            ],
            [
                'plan_id' => 7,
                'service_id' => 3,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 12,
                'price' => null,
                'unit' => 'hours',
                'description' => 'Max 100 persons',
            ],
            [
                'plan_id' => 7,
                'service_id' => 3,
                'type' => PlanServiceType::OPTION,
                'stripe_price_id' => Str::random(),
                'value' => 3,
                'price' => 5000,
                'unit' => 'hours',
                'description' => 'Max 50 persons',
            ],
            [
                'plan_id' => 7,
                'service_id' => 4,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 3,
                'price' => null,
                'unit' => 'times',
                'description' => null,
            ],
            [
                'plan_id' => 7,
                'service_id' => 4,
                'type' => PlanServiceType::OPTION,
                'stripe_price_id' => Str::random(),
                'value' => 1,
                'price' => 5000,
                'unit' => 'times',
                'description' => null,
            ],
            [
                'plan_id' => 7,
                'service_id' => 5,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 1,
                'price' => null,
                'unit' => null,
                'description' => null,
            ],
            [
                'plan_id' => 7,
                'service_id' => 6,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 100,
                'price' => null,
                'unit' => 'GB',
                'description' => null,
            ],
            [
                'plan_id' => 7,
                'service_id' => 6,
                'type' => PlanServiceType::OPTION,
                'stripe_price_id' => Str::random(),
                'value' => 10,
                'price' => 700,
                'unit' => 'GB',
                'description' => null,
            ],
            [
                'plan_id' => 7,
                'service_id' => 7,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 10,
                'price' => null,
                'unit' => 'cases',
                'description' => null,
            ],
            [
                'plan_id' => 7,
                'service_id' => 8,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 1,
                'price' => null,
                'unit' => null,
                'description' => null,
            ],
            [
                'plan_id' => 7,
                'service_id' => 9,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 1,
                'price' => null,
                'unit' => null,
                'description' => null,
            ],
            [
                'plan_id' => 7,
                'service_id' => 10,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 1,
                'price' => null,
                'unit' => null,
                'description' => null,
            ],
            [
                'plan_id' => 7,
                'service_id' => 11,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 1,
                'price' => null,
                'unit' => null,
                'description' => null,
            ],
            [
                'plan_id' => 7,
                'service_id' => 12,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 1,
                'price' => null,
                'unit' => null,
                'description' => null,
            ],
            [
                'plan_id' => 7,
                'service_id' => 13,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 1,
                'price' => null,
                'unit' => null,
                'description' => null,
            ],
            [
                'plan_id' => 7,
                'service_id' => 14,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 5,
                'price' => null,
                'unit' => 'times',
                'description' => null,
            ],
        ]);

        /**
         * NEO - Premium Plan
         */
        $seederData = array_merge($seederData, [
            [
                'plan_id' => 8,
                'service_id' => 1,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 300,
                'price' => null,
                'unit' => 'persons',
                'description' => 'Max 500 persons',
            ],
            [
                'plan_id' => 8,
                'service_id' => 1,
                'type' => PlanServiceType::OPTION,
                'stripe_price_id' => Str::random(),
                'value' => 1,
                'price' => 500,
                'unit' => 'person',
                'description' => null,
            ],
            [
                'plan_id' => 8,
                'service_id' => 2,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 1,
                'price' => null,
                'unit' => null,
                'description' => null,
            ],
            [
                'plan_id' => 8,
                'service_id' => 3,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 36,
                'price' => null,
                'unit' => 'hours',
                'description' => 'Max 500 persons',
            ],
            [
                'plan_id' => 8,
                'service_id' => 3,
                'type' => PlanServiceType::OPTION,
                'stripe_price_id' => Str::random(),
                'value' => 3,
                'price' => 5000,
                'unit' => 'hours',
                'description' => 'Max 50 persons',
            ],
            [
                'plan_id' => 8,
                'service_id' => 4,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 10,
                'price' => null,
                'unit' => 'times',
                'description' => null,
            ],
            [
                'plan_id' => 8,
                'service_id' => 4,
                'type' => PlanServiceType::OPTION,
                'stripe_price_id' => Str::random(),
                'value' => 1,
                'price' => 5000,
                'unit' => 'times',
                'description' => null,
            ],
            [
                'plan_id' => 8,
                'service_id' => 5,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 1,
                'price' => null,
                'unit' => null,
                'description' => null,
            ],
            [
                'plan_id' => 8,
                'service_id' => 6,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 200,
                'price' => null,
                'unit' => 'GB',
                'description' => null,
            ],
            [
                'plan_id' => 8,
                'service_id' => 6,
                'type' => PlanServiceType::OPTION,
                'stripe_price_id' => Str::random(),
                'value' => 10,
                'price' => 700,
                'unit' => 'GB',
                'description' => null,
            ],
            [
                'plan_id' => 8,
                'service_id' => 7,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 20,
                'price' => null,
                'unit' => 'cases',
                'description' => null,
            ],
            [
                'plan_id' => 8,
                'service_id' => 8,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 1,
                'price' => null,
                'unit' => null,
                'description' => null,
            ],
            [
                'plan_id' => 8,
                'service_id' => 9,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 1,
                'price' => null,
                'unit' => null,
                'description' => null,
            ],
            [
                'plan_id' => 8,
                'service_id' => 10,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 1,
                'price' => null,
                'unit' => null,
                'description' => null,
            ],
            [
                'plan_id' => 8,
                'service_id' => 11,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 1,
                'price' => null,
                'unit' => null,
                'description' => null,
            ],
            [
                'plan_id' => 8,
                'service_id' => 12,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 1,
                'price' => null,
                'unit' => null,
                'description' => null,
            ],
            [
                'plan_id' => 8,
                'service_id' => 13,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 1,
                'price' => null,
                'unit' => null,
                'description' => null,
            ],
            [
                'plan_id' => 8,
                'service_id' => 14,
                'type' => PlanServiceType::PLAN,
                'stripe_price_id' => null,
                'value' => 10,
                'price' => null,
                'unit' => 'times',
                'description' => null,
            ],
        ]);

        PlanService::insert($seederData);
    }
}
