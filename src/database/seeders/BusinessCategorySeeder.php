<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BusinessCategory;
use DB;

class BusinessCategorySeeder extends Seeder
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
        DB::table('business_categories')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Insert pre-defined attributes
        BusinessCategory::insert([
            [//1
                'business_category_code' => '0101',
                'business_category_name' => '農業',
                'business_category_example' => '耕種農業、畜産農業、農業・園芸サービス業等',
            ],
            [//2
                'business_category_code' => '0102',
                'business_category_name' => '林業',
                'business_category_example' => '育林業、素材生産業、製薪炭業、林業サービス業等',
            ],
            [//3
                'business_category_code' => '0103',
                'business_category_name' => '漁業・水産養殖業',
                'business_category_example' => '海面漁業、内水面漁業、海面養殖業、内水面養殖業',
            ],
            [//4
                'business_category_code' => '0201',
                'business_category_name' => '鉱業、採石業、砂利採取業',
                'business_category_example' => '金属鉱業 （金、銀、鉛、亜鉛、鉄、タングステン等）、石炭・亜炭鉱業（炭鉱等）、原油・天然ガス鉱業、採石業、砂・砂利・玉石採取業等',
            ],
            [//5
                'business_category_code' => '0301',
                'business_category_name' => '建設業',
                'business_category_example' => '総合工事業（一般土木建築工事業、舗装工事業、木造建築工事業等）、職別工事業（塗装工事業、床・内装工事業等）、設備工事業（電気工事業、電気通信・信号装置工事業等）',
            ],
            [//6
                'business_category_code' => '0401',
                'business_category_name' => '食料品製造業',
                'business_category_example' => '畜産食料品、水産食料品、精穀・製粉、調味料等',
            ],
            [//7
                'business_category_code' => '0402',
                'business_category_name' => '飲料製造業',
                'business_category_example' => '清涼飲料、酒類等',
            ],
            [//8
                'business_category_code' => '0403',
                'business_category_name' => 'たばこ製造業',
                'business_category_example' => '',
            ],
            [//9
                'business_category_code' => '0404',
                'business_category_name' => '飼料・有機質肥料製造業',
                'business_category_example' => '配合飼料、有機質肥料等',
            ],
            [//10
                'business_category_code' => '0501',
                'business_category_name' => '製糸業、紡績業、化学繊維・ねん糸等製造業',
                'business_category_example' => '製糸、化学繊維、炭素繊維、綿紡績、化学繊維紡績、毛紡績、ねん糸等',
            ],
            [//11
                'business_category_code' => '0502',
                'business_category_name' => '織物業、ニット生地製造業',
                'business_category_example' => '綿・スフ織物、絹・人絹織物、毛織物、細幅織物、丸編ニット生地等',
            ],
            [//12
                'business_category_code' => '0503',
                'business_category_name' => '染色整理業、綱・網・レース・繊維粗製品製造業',
                'business_category_example' => '染色・整理、綱、網、レース、フェルト・不織布、繊維粗製品等',
            ],
            [//13
                'business_category_code' => '0504',
                'business_category_name' => '衣服・その他の繊維製品製造業',
                'business_category_example' => '織物製外衣、ニット製外衣、下着、和装製品、寝具、じゅうたん等',
            ],
            [//14
                'business_category_code' => '0601',
                'business_category_name' => '木材・木製品製造業',
                'business_category_example' => '一般製材、ベニヤ板、合板、パーティクルボード等',
            ],
            [//15
                'business_category_code' => '0602',
                'business_category_name' => 'パルプ・紙製造業',
                'business_category_example' => 'パルプ、洋紙、板紙、和紙',
            ],
            [//16
                'business_category_code' => '0603',
                'business_category_name' => '紙加工品製造業',
                'business_category_example' => '段ボール、壁紙、事務用・学用紙、紙製容器等',
            ],
            [//17
                'business_category_code' => '0701',
                'business_category_name' => '化学肥料製造業',
                'business_category_example' => '窒素質・りん酸質肥料、複合肥料等',
            ],
            [//18
                'business_category_code' => '0702',
                'business_category_name' => '無機化学工業製品製造業',
                'business_category_example' => 'ソーダ、無機顔料、圧縮ガス・液化ガス、リン酸、塩等',
            ],
            [//19
                'business_category_code' => '0703',
                'business_category_name' => '有機化学工業製品製造業',
                'business_category_example' => 'エチレン等石油化学系基礎製品、脂肪族系中間物、エチルアルコール、フェノール樹脂等プラスチック、合成ゴム等',
            ],
            [//20
                'business_category_code' => '0704',
                'business_category_name' => '油脂加工製品・石けん・合成洗剤・界面活性剤・塗料製造業',
                'business_category_example' => '脂肪酸、グリセリン、石けん、合成洗剤、界面活性剤、塗料、印刷インキ、ろうそく等',
            ],
            [//21
                'business_category_code' => '0705',
                'business_category_name' => '医薬品製造業',
                'business_category_example' => '医薬品、ワクチン、生薬・漢方製剤等',
            ],
            [//22
                'business_category_code' => '0706',
                'business_category_name' => '化粧品・歯磨、その他の化粧用調整品製造業',
                'business_category_example' => '化粧品、シャンプー、歯磨等',
            ],
            [//23
                'business_category_code' => '0707',
                'business_category_name' => 'その他の化学工業',
                'business_category_example' => '火薬類、農薬、ゼラチン、接着剤、写真感光材料等',
            ],
            [//24
                'business_category_code' => '0801',
                'business_category_name' => '石油精製業',
                'business_category_example' => 'ガソリン、ナフサ、灯油、軽油、重油等',
            ],
            [//25
                'business_category_code' => '0802',
                'business_category_name' => 'その他の石油製品・石炭製品製造業',
                'business_category_example' => '潤滑油、グリース、コークス、練炭、豆炭、舗装材料等',
            ],
            [//26
                'business_category_code' => '0901',
                'business_category_name' => 'ガラス・同製品製造業',
                'business_category_example' => '板ガラス、ガラス容器、理化学用・医療用ガラス器具等',
            ],
            [//27
                'business_category_code' => '0902',
                'business_category_name' => 'セメント・同製品製造業',
                'business_category_example' => 'セメント、生コンクリート、コンクリート製品等',
            ],
            [//28
                'business_category_code' => '0903',
                'business_category_name' => 'その他の窯業・土石製品製造業',
                'business_category_example' => '陶磁器・同関連製品、建設用粘土製品、耐火物、炭素・黒鉛製品、研磨材等',
            ],
            [//29
                'business_category_code' => '1001',
                'business_category_name' => '銑鉄・粗鋼・鋼材製造業',
                'business_category_example' => '銑鉄、粗鋼、鋼材、鋼管等',
            ],
            [//30
                'business_category_code' => '1002',
                'business_category_name' => '鋳鍛造品・その他の鉄鋼製品製造業',
                'business_category_example' => '銑鉄鋳物、鋳鋼等鉄素形材、銑鋼シャースリット等',
            ],
            [//31
                'business_category_code' => '1101',
                'business_category_name' => '非鉄金属製錬・精製業',
                'business_category_example' => '銅、鉛、亜鉛、貴金属、ニッケル、アルミニウム等',
            ],
            [//32
                'business_category_code' => '1102',
                'business_category_name' => 'その他の非鉄金属製品製造業',
                'business_category_example' => '伸銅品等非鉄金属・同合金圧延製品、電線、ケーブル、非鉄金属鋳物、非鉄金属鍛造品',
            ],
            [//33
                'business_category_code' => '1201',
                'business_category_name' => '建設用・建築用金属製品製造業',
                'business_category_example' => '鉄骨、鉄塔、橋りょう等建設用金属製品、金属製サッシ・ドア、鉄骨系プレハブ住宅、建築用金属製品等',
            ],
            [//34
                'business_category_code' => '1202',
                'business_category_name' => 'その他の金属製品製造業',
                'business_category_example' => 'ブリキ缶、めっき板、洋食器、刃物、金物、暖房装置、金属素形材、金属線製品、ボルト、ナット、リベット等',
            ],
            [//35
                'business_category_code' => '1301',
                'business_category_name' => '一般産業用機械・装置製造業',
                'business_category_example' => 'エレベータ、エスカレータ、コンベヤ、工業窯炉、冷凍機、湿潤調整装置等',
            ],
            [//36
                'business_category_code' => '1302',
                'business_category_name' => 'その他のはん用機械器具製造業',
                'business_category_example' => 'ボイラ、原動機、ポンプ、圧縮機、消火器、軸受等',
            ],
            [//37
                'business_category_code' => '1401',
                'business_category_name' => '農業用機械、建設機械・鉱山機械、繊維機械製造業',
                'business_category_example' => '農業用機械、建設機械、鉱山機械、化学繊維機械、紡績機械、製織機械、編組機械、染色整理仕上機械、縫製機械等',
            ],
            [//38
                'business_category_code' => '1402',
                'business_category_name' => '生活関連産業用機械・基礎素材産業用機械製造業',
                'business_category_example' => '食品機械、木材加工機械、パルプ装置・製紙機械、印刷・製本・紙工機械、包装・荷造機械、鋳造装置、化学機械、プラスチック加工機械等',
            ],
            [//39
                'business_category_code' => '1403',
                'business_category_name' => '金属加工機械製造業',
                'business_category_example' => '旋盤、ボール盤等金属工作機械、圧延機械、ベンディングマシン等金属加工機械等',
            ],
            [//40
                'business_category_code' => '1404',
                'business_category_name' => '半導体・フラットパネルディスプレイ製造装置製造業',
                'business_category_example' => 'ウェーハプロセス装置、半導体製造装置、フラットパネルディスプレイ製造装置',
            ],
            [//41
                'business_category_code' => '1405',
                'business_category_name' => 'その他の生産用機械器具製造業',
                'business_category_example' => '金型、真空装置、ロボット等',
            ],
            [//42
                'business_category_code' => '1501',
                'business_category_name' => '事務用・サービス用・娯楽用機械器具製造業',
                'business_category_example' => '複写機等事務用機械器具、営業用洗濯機、自動車洗浄機、遊園施設機械、自動販売機、両替機、自動ドア等',
            ],
            [//43
                'business_category_code' => '1502',
                'business_category_name' => '光学機械器具・レンズ製造業',
                'business_category_example' => 'カメラ、顕微鏡、望遠鏡、映画用機械、光学機械用レンズ、プリズム等',
            ],
            [//44
                'business_category_code' => '1503',
                'business_category_name' => 'その他の業務用機械器具製造業',
                'business_category_example' => '計量器、測定器、分析機器、試験機、測定機械器具、理化学機械器具、医療用機械器具、武器等',
            ],
            [//45
                'business_category_code' => '1601',
                'business_category_name' => '産業用電気機械器具製造業',
                'business_category_example' => '発電機、電動機、その他の回転電気機械、変圧器類、電力開閉装置、配電盤、分電盤、電気溶接機、電気炉等',
            ],
            [//46
                'business_category_code' => '1602',
                'business_category_name' => '民生用電気機械器具製造業',
                'business_category_example' => '電子レンジ、冷蔵庫、電気がま、扇風機、電気温水器、エアコン、洗濯機、掃除機、アイロン、電気ストーブ等',
            ],
            [//47
                'business_category_code' => '1603',
                'business_category_name' => '電子応用装置製造業',
                'business_category_example' => 'X線装置、医療用電子応用装置、電子顕微鏡等その他の電子応用装置',
            ],
            [//48
                'business_category_code' => '1604',
                'business_category_name' => 'その他の電気機械器具製造業',
                'business_category_example' => '電球、蛍光灯等電球・電気照明器具、蓄電池、乾電池、電気計測器、工業計器、化学分析機器、永久磁石等',
            ],
            [//49
                'business_category_code' => '1701',
                'business_category_name' => '通信機械器具・同関連機械器具、映像・音響機械器具製造業',
                'business_category_example' => '電話機、ファクシミリ等有線通信機械器具、ラジオ・テレビ放送装置、携帯電話等無線通信機械器具、ラジオ・テレビ受信機、ビデオ機器、デジタルカメラ、ステレオ、カラオケ等電気音響機器等',
            ],
            [//50
                'business_category_code' => '1702',
                'business_category_name' => '電子計算機・同附属装置製造業',
                'business_category_example' => '電子計算機、パーソナルコンピュータ、磁気ディスク装置、光ディスク装置等外部記憶装置等',
            ],
            [//51
                'business_category_code' => '1703',
                'business_category_name' => '電子部品・デバイス・電子回路製造業',
                'business_category_example' => 'ブラウン管等電子管、ダイオード、トランジスタ、集積回路、液晶パネル・フラットパネル、抵抗器、コンデンサ、変成器、磁気ヘッド、半導体メモリメディア、光ディスク、電子回路基盤、ユニット部品等',
            ],
            [//52
                'business_category_code' => '1801',
                'business_category_name' => '自動車、自動車車体・附随車製造業',
                'business_category_example' => '乗用車、バス、トラック、二輪自動車、トレーラ',
            ],
            [//53
                'business_category_code' => '1802',
                'business_category_name' => '自動車部分品・附属品製造業',
                'business_category_example' => '自動車エンジン、ブレーキ、クラッチ車軸、ラジエータ、デファレンシャルギア等',
            ],
            [//54
                'business_category_code' => '1803',
                'business_category_name' => 'その他の輸送用機械器具製造業',
                'business_category_example' => '鉄道車輌・同部品、船舶、船用機関、航空機・同附属品、産業用車輌・同部分品附属品、自転車・同部分品等',
            ],
            [//55
                'business_category_code' => '1901',
                'business_category_name' => '家具・装備品製造業',
                'business_category_example' => '家具、宗教用具、建具等',
            ],
            [//56
                'business_category_code' => '1902',
                'business_category_name' => '印刷・同関連業',
                'business_category_example' => '印刷業、製版業、製本業、印刷物加工業、印刷関連サービス業',
            ],
            [//57
                'business_category_code' => '1903',
                'business_category_name' => 'プラスチック製品製造業',
                'business_category_example' => 'プラスチック板・棒・管・継手・異形押出製品・フィルム・シート・床材、合成皮革、工業用プラスチック製品、発泡・強化プラスチック製品、プラスチック成型材料等',
            ],
            [//58
                'business_category_code' => '1904',
                'business_category_name' => 'ゴム製品製造業',
                'business_category_example' => 'タイヤ、チューブ、ゴム製・プラスチック製履物、ゴムベルト、ゴムホース、工業用ゴム製品等',
            ],
            [//59
                'business_category_code' => '1905',
                'business_category_name' => 'なめし革・同製品・毛皮製造業',
                'business_category_example' => 'なめし革、工業用革製品、革製履物、革製手袋、かばん、袋物、毛皮等',
            ],
            [//60
                'business_category_code' => '1906',
                'business_category_name' => 'その他の製造業',
                'business_category_example' => '貴金属・宝石製品、装身具、装飾品、ボタン、時計、楽器、がん具、眼鏡、運動用具、ペン・鉛筆等事務用品、漆器、畳等生活雑貨製品等',
            ],
            [//61
                'business_category_code' => '2001',
                'business_category_name' => '電気業、ガス業、熱供給業、水道業',
                'business_category_example' => '発電所、変電所、ガス製造工場、ガス供給所、熱供給業、上水道業、工業用水道業、下水道業',
            ],
            [//62
                'business_category_code' => '2101',
                'business_category_name' => '通信業',
                'business_category_example' => '固定電気通信業、移動電気通信業等',
            ],
            [//63
                'business_category_code' => '2102',
                'business_category_name' => '放送業',
                'business_category_example' => '公共放送業、民間放送業、有線放送業',
            ],
            [//64
                'business_category_code' => '2103',
                'business_category_name' => '情報サービス業',
                'business_category_example' => 'ソフトウェア業、情報処理・提供サービス業',
            ],
            [//65
                'business_category_code' => '2104',
                'business_category_name' => 'インターネット附随サービス業',
                'business_category_example' => 'ポータルサイト・サーバ運営業、アプリケーション・サービス・プロバイダ、ウェブ・コンテンツ提供業、電子認証業、情報ネットワーク・セキュリティ・サービス業等',
            ],
            [//66
                'business_category_code' => '2105',
                'business_category_name' => '映像・音声・文字情報制作業',
                'business_category_example' => '映像情報制作・配給業、音声情報制作業、新聞業、出版業、広告制作業等',
            ],
            [//67
                'business_category_code' => '2201',
                'business_category_name' => '鉄道業、道路旅客運送業、道路貨物運送業、水運業、航空運輸業、郵便業',
                'business_category_example' => '鉄道業、道路旅客運送業、道路貨物運送業、水運業、航空運輸業、郵便業',
            ],
            [//68
                'business_category_code' => '2202',
                'business_category_name' => '倉庫業・運輸に附帯するサービス業',
                'business_category_example' => '倉庫業、港湾運送業、貨物運送取扱業、運送代理店、こん包業、運輸施設提供業等',
            ],
            [//69
                'business_category_code' => '2301',
                'business_category_name' => '卸売業',
                'business_category_example' => '各種商品卸売業、機械器具卸売業等',
            ],
            [//70
                'business_category_code' => '2302',
                'business_category_name' => '小売業',
                'business_category_example' => '各種商品小売業、飲食料品小売業、機械器具小売業等',
            ],
            [//71
                'business_category_code' => '2401',
                'business_category_name' => '金融業、保険業',
                'business_category_example' => '銀行業、協同組織金融業、貸金業、クレジットカード業、金融商品取引業、商品先物取引業、保険業等',
            ],
            [//72
                'business_category_code' => '2501',
                'business_category_name' => '不動産業',
                'business_category_example' => '不動産取引業、不動産賃貸・管理業',
            ],
            [//73
                'business_category_code' => '2601',
                'business_category_name' => '物品賃貸業',
                'business_category_example' => '各種物品賃貸業、産業用機械器具賃貸業、自動車賃貸業等',
            ],
            [//74
                'business_category_code' => '2701',
                'business_category_name' => '宿泊業',
                'business_category_example' => '旅館、ホテル等',
            ],
            [//75
                'business_category_code' => '2702',
                'business_category_name' => '飲食店',
                'business_category_example' => '食堂、レストラン、専門料理店、酒場・ビヤホール、喫茶店等',
            ],
            [//76
                'business_category_code' => '2703',
                'business_category_name' => '持ち帰り・配達飲食サービス業',
                'business_category_example' => '持ち帰り飲食サービス業、配達飲食サービス業',
            ],
            [//77
                'business_category_code' => '2801',
                'business_category_name' => '教育、学習支援',
                'business_category_example' => '幼稚園、学校、学習塾、技能教授所等',
            ],
            [//78
                'business_category_code' => '2802',
                'business_category_name' => '医療、福祉',
                'business_category_example' => '病院、保健所、保育所、介護老人保健施設、障害者支援施設等',
            ],
            [//79
                'business_category_code' => '2803',
                'business_category_name' => '複合サービス業',
                'business_category_example' => '郵便局、協同組合',
            ],
            [//80
                'business_category_code' => '2901',
                'business_category_name' => '経営コンサルタント業、純粋持株会社',
                'business_category_example' => '経営コンサルタント業、純粋持株会社',
            ],
            [//81
                'business_category_code' => '2902',
                'business_category_name' => '広告業',
                'business_category_example' => '総合広告業、広告代理業、新聞広告代理業、インターネット広告業等',
            ],
            [//82
                'business_category_code' => '2903',
                'business_category_name' => '学術研究、専門・技術サービス業（経営コンサルタント業、純粋持株会社、広告業は除く。）',
                'business_category_example' => '学術・開発研究機関、法律事務所、特許事務所、公認会計士事務所、税理士事務所、デザイン業、著述家業、興信所、翻訳業、獣医業、建築設計業、機械設計業、写真業等',
            ],
            [//83
                'business_category_code' => '2904',
                'business_category_name' => '生活関連サービス業、娯楽業',
                'business_category_example' => '洗濯・理容・美容・浴場業、旅行業、物品預り業、冠婚葬祭業、映画館、劇場、スポーツ施設提供業、公園、遊園地、遊戯場等',
            ],
            [//84
                'business_category_code' => '2905',
                'business_category_name' => 'その他のサービス業',
                'business_category_example' => '廃棄物処理業、自動車整備業、機械等修理業、職業紹介・労働者派遣業、速記業、複写業、警備業、政治・経済・文化団体、宗教等',
            ],
        ]);
    }
}
