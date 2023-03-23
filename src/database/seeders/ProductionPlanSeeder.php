<?php

namespace Database\Seeders;

use App\Enums\EntityType;
use App\Models\Plan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductionPlanSeeder extends Seeder
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
        DB::table('plans')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $seederData = [];

        /**
         * RIO - Plans
         */
        $seederData = array_merge($seederData, [
            [
                'name' => 'フリープラン',
                'entity_type' => EntityType::RIO,
                'stripe_price_id' => 'price_1LnDEAKSvOR9MyGuaBHhZrMt',
                'price' => 0,
                'description' => 'アカウント開設時の個人の無料プラン',
            ],
            [
                'name' => 'ライトプラン',
                'entity_type' => EntityType::RIO,
                'stripe_price_id' => 'price_1LnDEAKSvOR9MyGuwlsZlJfv',
                'price' => 500,
                'description' => '電子契約をオプション利用できる個人のワンコインプラン',
            ],
            [
                'name' => 'スタンダードプラン',
                'entity_type' => EntityType::RIO,
                'stripe_price_id' => 'price_1LnDEAKSvOR9MyGuvyaeKRs6',
                'price' => 2000,
                'description' => '標準でネットショップも利用できる個人のお手軽プラン',
            ],
            [
                'name' => 'プレミアムプラン',
                'entity_type' => EntityType::RIO,
                'stripe_price_id' => 'price_1LnDEAKSvOR9MyGuGukwIZly',
                'price' => 5000,
                'description' => '帳票発行もネットショップも利用できる個人のオススメプラン',
            ],
        ]);

        /**
         * NEO - Plans
         */
        $seederData = array_merge($seederData, [
            [
                'name' => 'フリープラン',
                'entity_type' => EntityType::NEO,
                'stripe_price_id' => 'price_1LnDEJKSvOR9MyGuslnlBO97',
                'price' => 0,
                'description' => 'まずは試しに利用してみたい方へおすすめ',
            ],
            [
                'name' => 'ライトプラン',
                'entity_type' => EntityType::NEO,
                'stripe_price_id' => 'price_1LnDEJKSvOR9MyGu44vMYsy5',
                'price' => 10000,
                'description' => '標準でネットショップも利用できるお手軽プラン',
            ],
            [
                'name' => 'スタンダードプラン',
                'entity_type' => EntityType::NEO,
                'stripe_price_id' => 'price_1LnDEJKSvOR9MyGuuKC9EONu',
                'price' => 20000,
                'description' => '十分な機能と容量を備えた標準プラン',
            ],
            [
                'name' => 'プレミアムプラン',
                'entity_type' => EntityType::NEO,
                'stripe_price_id' => 'price_1LnDEJKSvOR9MyGuW002uwEK',
                'price' => 30000,
                'description' => '大規模なチームでの本格的な利用におすすめ',
            ],
        ]);

        Plan::insert($seederData);
    }
}
