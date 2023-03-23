<?php

namespace Database\Seeders;

use App\Models\ClassifiedSaleCategory;
use Illuminate\Database\Seeder;
use DB;

class ClassifiedSaleCategorySeeder extends Seeder
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
        DB::table('classified_sale_categories')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Insert pre-defined attributes
        ClassifiedSaleCategory::insert([
            [
                'sale_category_name' => '役務（作業・工事・施術・コンサル'
            ],
            [
                'sale_category_name' => '会費、月謝、チケット'
            ],
            [
                'sale_category_name' => 'ファッション'
            ],
            [
                'sale_category_name' => '食品'
            ],
            [
                'sale_category_name' => 'アウトドア、釣り、旅行用品'
            ],
            [
                'sale_category_name' => 'ダイエット、健康'
            ],
            [
                'sale_category_name' => 'コスメ、美容、ヘアケア'
            ],
            [
                'sale_category_name' => 'スマホ、タブレット、パソコン'
            ],
            [
                'sale_category_name' => 'テレビ、オーディオ、カメラ'
            ],
            [
                'sale_category_name' => '家電'
            ],
            [
                'sale_category_name' => '家具、インテリア'
            ],
            [
                'sale_category_name' => '花、ガーデニング'
            ],
            [
                'sale_category_name' => 'キッチン、日用品、文具'
            ],
            [
                'sale_category_name' => 'DIY、工具'
            ],
            [
                'sale_category_name' => 'ペット用品'
            ],
            [
                'sale_category_name' => '楽器、手芸、コレクション'
            ],
            [
                'sale_category_name' => 'ゲーム、おもちゃ'
            ],
            [
                'sale_category_name' => 'ベビー、キッズ、マタニティ'
            ],
            [
                'sale_category_name' => 'スポーツ'
            ],
            [
                'sale_category_name' => '車、バイク、自転車'
            ],
            [
                'sale_category_name' => 'CD、音楽ソフト'
            ],
            [
                'sale_category_name' => 'DVD、映像ソフト'
            ],
            [
                'sale_category_name' => '本、雑誌、コミック'
            ],
            [
                'sale_category_name' => 'レンタル・各種貸し出しサービス'
            ],
            [
                'sale_category_name' => 'その他'
            ],
        ]);
    }
}
