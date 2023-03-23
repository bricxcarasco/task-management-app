<?php

namespace App\Helpers;

class Constant
{
    public const RANDOM_HASH_CHARACTERS = 40;

    public const RIO_UPDATE_IDS = [1, 2, 3, 11];
    public const RIO_PROFILE_UPDATE_IDS = [4, 5, 6, 10, 12, 13, 14, 15];

    public const PREFECTURES = [
        [
            'id' => 0,
            'name' => '日本以外'
        ],
        [
            'id' => 1,
            'name' => '北海道'
        ],
        [
            'id' => 2,
            'name' => '青森県'
        ],
        [
            'id' => 3,
            'name' => '岩手県'
        ],
        [
            'id' => 4,
            'name' => '宮城県'
        ],
        [
            'id' => 5,
            'name' => '秋田県'
        ],
        [
            'id' => 6,
            'name' => '山形県'
        ],
        [
            'id' => 7,
            'name' => '福島県'
        ],
        [
            'id' => 8,
            'name' => '茨城県'
        ],
        [
            'id' => 9,
            'name' => '栃木県'
        ],
        [
            'id' => 10,
            'name' => '群馬県'
        ],
        [
            'id' => 11,
            'name' => '埼玉県'
        ],
        [
            'id' => 12,
            'name' => '千葉県'
        ],
        [
            'id' => 13,
            'name' => '東京都'
        ],
        [
            'id' => 14,
            'name' => '神奈川県'
        ],
        [
            'id' => 15,
            'name' => '新潟県'
        ],
        [
            'id' => 16,
            'name' => '富山県'
        ],
        [
            'id' => 17,
            'name' => '石川県'
        ],
        [
            'id' => 18,
            'name' => '福井県'
        ],
        [
            'id' => 19,
            'name' => '山梨県'
        ],
        [
            'id' => 20,
            'name' => '長野県'
        ],
        [
            'id' => 21,
            'name' => '岐阜県'
        ],
        [
            'id' => 22,
            'name' => '静岡県'
        ],
        [
            'id' => 23,
            'name' => '愛知県'
        ],
        [
            'id' => 24,
            'name' => '三重県'
        ],
        [
            'id' => 25,
            'name' => '滋賀県'
        ],
        [
            'id' => 26,
            'name' => '京都府'
        ],
        [
            'id' => 27,
            'name' => '大阪府'
        ],
        [
            'id' => 28,
            'name' => '兵庫県'
        ],
        [
            'id' => 29,
            'name' => '奈良県'
        ],
        [
            'id' => 30,
            'name' => '和歌山県'
        ],
        [
            'id' => 31,
            'name' => '鳥取県'
        ],
        [
            'id' => 32,
            'name' => '島根県'
        ],
        [
            'id' => 33,
            'name' => '岡山県'
        ],
        [
            'id' => 34,
            'name' => '広島県'
        ],
        [
            'id' => 35,
            'name' => '山口県'
        ],
        [
            'id' => 36,
            'name' => '徳島県'
        ],
        [
            'id' => 37,
            'name' => '香川県'
        ],
        [
            'id' => 38,
            'name' => '愛媛県'
        ],
        [
            'id' => 39,
            'name' => '高知県'
        ],
        [
            'id' => 40,
            'name' => '福岡県'
        ],
        [
            'id' => 41,
            'name' => '佐賀県'
        ],
        [
            'id' => 42,
            'name' => '長崎県'
        ],
        [
            'id' => 43,
            'name' => '熊本県'
        ],
        [
            'id' => 44,
            'name' => '大分県'
        ],
        [
            'id' => 45,
            'name' => '宮崎県'
        ],
        [
            'id' => 46,
            'name' => '鹿児島県'
        ],
        [
            'id' => 47,
            'name' => '沖縄県'
        ]
    ];

    public const SECRET_QUESTIONS = [
        [
            'id' => 1,
            'value' => '両親が出会った町の名前は？'
        ],
        [
            'id' => 2,
            'value' => '初めての職場での上司の名前は？'
        ],
        [
            'id' => 2,
            'value' => '子供時代を過ごした町の名前は？'
        ]
    ];

    public const MAIL_TEMPLATES = [
        'SIGNUP_EMAIL_VERIFICATION' => 1,
        'REGISTRATION_VERIFIED' => 2,
    ];

    public const CUSTOM_ERROR_MESSAGES = [
        'EMAIL_VERIFY_PAGE_EXPIRED' => 'メール確認の有効期限が切れています。再登録してください。'
    ];

    public const STATUS_MESSAGES = [
        401 => 'このサーバーへのアクセスは許可されていません。',
        500 => '',
    ];
}
