<?php

namespace Database\Seeders;

use App\Enums\AttributeCodes;
use Illuminate\Database\Seeder;
use App\Models\ExpertAttribute;
use DB;

class ExpertAttributeSeeder extends Seeder
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
        DB::table('expert_attributes')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Insert pre-defined attributes
        ExpertAttribute::insert([
            [
                'attribute_code' => AttributeCodes::EDUCATIONAL_BACKGROUND,
                'attribute_name' => '学歴',
                'is_searchable' => true,
            ],
            [
                'attribute_code' => AttributeCodes::NEO_AFFILIATES,
                'attribute_name' => '所属NEO',
                'is_searchable' => true,
            ],
            [
                'attribute_code' => AttributeCodes::QUALIFICATIONS,
                'attribute_name' => '取得資格',
                'is_searchable' => true,
            ],
            [
                'attribute_code' => AttributeCodes::SKILLS,
                'attribute_name' => '取得技能',
                'is_searchable' => true,
            ],
            [
                'attribute_code' => AttributeCodes::AWARDS,
                'attribute_name' => '表彰履歴',
                'is_searchable' => false,
            ],
            [
                'attribute_code' => AttributeCodes::EXPERIENCE,
                'attribute_name' => '専門経験年数',
                'is_searchable' => true,
            ],
            [
                'attribute_code' => AttributeCodes::PROFESSION,
                'attribute_name' => '職業',
                'is_searchable' => true,
            ],
            [
                'attribute_code' => AttributeCodes::URL,
                'attribute_name' => 'URL',
                'is_searchable' => false,
            ],
            [
                'attribute_code' => AttributeCodes::INDUSTRY,
                'attribute_name' => '業種',
                'is_searchable' => true,
            ],
            [
                'attribute_code' => AttributeCodes::BUSINESS_HOURS,
                'attribute_name' => '営業時間、休日情報',
                'is_searchable' => true,
            ],
            [
                'attribute_code' => AttributeCodes::OVERSEAS,
                'attribute_name' => '海外対応',
                'is_searchable' => true,
            ],
            [
                'attribute_code' => AttributeCodes::PRODUCT_SERVICE_INFORMATION,
                'attribute_name' => '取扱商品・サービス情報',
                'is_searchable' => true,
            ],
            [
                'attribute_code' => AttributeCodes::HISTORY,
                'attribute_name' => '沿革',
                'is_searchable' => false,
            ],
            [
                'attribute_code' => AttributeCodes::EMAIL,
                'attribute_name' => 'メール',
                'is_searchable' => true,
            ],
        ]);
    }
}
