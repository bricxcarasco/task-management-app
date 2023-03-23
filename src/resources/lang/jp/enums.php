<?php

use App\Enums\BasicSettings;
use App\Enums\Chat\ChatTypes;
use App\Enums\Classified\MessageSender;
use App\Enums\Classified\PaymentMethods;
use App\Enums\Classified\PaymentStatuses;
use App\Enums\Classified\SaleProductAccessibility;
use App\Enums\Classified\SaleProductVisibility;
use App\Enums\Classified\SettingTypes;
use App\Enums\Connection\GroupStatuses;
use App\Enums\Connection\ListFilters;
use App\Enums\Form\ProductTaxDistinction;
use App\Enums\Form\Types;
use App\Enums\Rio\DisplayType;
use App\Enums\Rio\GenderType;
use App\Enums\Rio\SecretQuestionType;
use App\Enums\Rio\AcceptConnectionType as RioAcceptConnectionType;
use App\Enums\Rio\AcceptConnectionByNeoType;
use App\Enums\PrefectureTypes;
use App\Enums\YearsOfExperiences;
use App\Enums\Neo\OrganizationAttributeType;
use App\Enums\Neo\AcceptConnectionType;
use App\Enums\Neo\AcceptBelongType;
use App\Enums\Neo\AcceptParticipationType;
use App\Enums\Neo\OverseasSupportType;
use App\Enums\Neo\RestrictConnectionType;
use App\Enums\Neo\RoleType;
use App\Enums\Task\Priorities;
use App\Enums\Workflow\ApproverStatusTypes;
use App\Enums\Workflow\StatusTypes;
use App\Enums\Workflow\ReactionTypes;

return [
    BasicSettings::class => [
        BasicSettings::CONNECTION_APPLICATION => 'つながり申請の受信',
        BasicSettings::CONNECTION_GROUP_INVITATION => 'つながりグループ招待の受信',
        BasicSettings::NEO_INVITATION => 'NEO招待の受信',
        BasicSettings::CHAT_MESSAGE => 'チャットメッセージの受信',
        BasicSettings::ADD_DOCUMENT_SHARING => '文書管理への共有追加',
        BasicSettings::SCHEDULE_INVITATION => 'スケジュールの参加申請',
        BasicSettings::CHANGE_EMAIL => 'メールアドレスの変更',
        BasicSettings::CHANGE_PASSWORD => 'パスワードの変更',
        BasicSettings::NETSHOP_PURCHASE => 'Netshopで購入したもの',
        BasicSettings::NETSHOP_CHAT_MESSAGE => 'Netshopからのメッセージを受信',
        BasicSettings::FORM_RECIPIENT_CONNECTION => 'フォームで受信者接続として選択される',
    ],
    ChatTypes::class => [
        ChatTypes::CONNECTED => 'つながり',
        ChatTypes::CONNECTED_GROUP => 'つながりグループ',
        ChatTypes::NEO_GROUP => 'NEOチーム',
        ChatTypes::NEO_MESSAGE => 'NEOメッセージ',
    ],
    GenderType::class => [
        GenderType::MALE => '男性',
        GenderType::FEMALE => '女性',
    ],
    GroupStatuses::class => [
        GroupStatuses::REQUESTING => '申請中',
        GroupStatuses::CONNECTED => 'つながり状態',
    ],
    PrefectureTypes::class => [
        PrefectureTypes::OTHER => '日本以外',
        PrefectureTypes::HOKKAIDO => '北海道',
        PrefectureTypes::AOMORI => '青森県',
        PrefectureTypes::IWATE => '岩手県',
        PrefectureTypes::MIYAGI => '宮城県',
        PrefectureTypes::AKITA => '秋田県',
        PrefectureTypes::YAMAGATA => '山形県',
        PrefectureTypes::FUKUSHIMA => '福島県',
        PrefectureTypes::IBARAKI => '茨城県',
        PrefectureTypes::TOCHIGI => '栃木県',
        PrefectureTypes::GUNMA => '群馬県',
        PrefectureTypes::SAITAMA => '埼玉県',
        PrefectureTypes::CHIBA => '千葉県',
        PrefectureTypes::TOKYO => '東京都',
        PrefectureTypes::KANAGAWA => '神奈川県',
        PrefectureTypes::NIIGATA => '新潟県',
        PrefectureTypes::TOYAMA => '富山県',
        PrefectureTypes::ISHIKAWA => '石川県',
        PrefectureTypes::FUKUI => '福井県',
        PrefectureTypes::YAMANASHI => '山梨県',
        PrefectureTypes::NAGANO => '長野県',
        PrefectureTypes::GIFU => '岐阜県',
        PrefectureTypes::SHIZUOKA => '静岡県',
        PrefectureTypes::AICHI => '愛知県',
        PrefectureTypes::MIE => '三重県',
        PrefectureTypes::SHIGA => '滋賀県',
        PrefectureTypes::KYOTO => '京都府',
        PrefectureTypes::OSAKA => '大阪府',
        PrefectureTypes::HYOGO => '兵庫県',
        PrefectureTypes::NARA => '奈良県',
        PrefectureTypes::WAKAYAMA => '和歌山県',
        PrefectureTypes::TOTTORI => '鳥取県',
        PrefectureTypes::SHIMANE => '島根県',
        PrefectureTypes::OKAYAMA => '岡山県',
        PrefectureTypes::HIROSHIMA => '広島県',
        PrefectureTypes::YAMAGUCHI => '山口県',
        PrefectureTypes::TOKUSHIMA => '徳島県',
        PrefectureTypes::KAGAWA => '香川県',
        PrefectureTypes::EHIME => '愛媛県',
        PrefectureTypes::KOCHI => '高知県',
        PrefectureTypes::FUKUOKA => '福岡県',
        PrefectureTypes::SAGA => '佐賀県',
        PrefectureTypes::NAGASAKI => '長崎県',
        PrefectureTypes::KUMAMOTO => '熊本県',
        PrefectureTypes::OITA => '大分県',
        PrefectureTypes::MIYAZAKI => '宮崎県',
        PrefectureTypes::KAGOSHIMA => '鹿児島県',
        PrefectureTypes::OKINAWA => '沖縄県',
    ],
    Priorities::class => [
        Priorities::LOW => '低',
        Priorities::MID => '中',
        Priorities::HIGH => '高',
    ],
    SecretQuestionType::class => [
        SecretQuestionType::FIRST_QUESTION => '両親が出会った町の名前は？',
        SecretQuestionType::SECOND_QUESTION => '初めての職場での上司の名前は？',
        SecretQuestionType::THIRD_QUESTION => '子供時代を過ごした町の名前は？',
    ],
    OrganizationAttributeType::class => [
        OrganizationAttributeType::CORPORATE => '法人格',
        OrganizationAttributeType::NPO => 'NPO',
        OrganizationAttributeType::SCHOOL => 'スクール',
        OrganizationAttributeType::ALUMNI => 'OB会',
    ],
    RioAcceptConnectionType::class => [
        RioAcceptConnectionType::ACCEPT_APPLICATION => '全RIOからのつながり申請を受け付ける',
        RioAcceptConnectionType::ACCEPT_CONNECTION => 'つながりのつながりまでのRIOからの申請を受け付ける',
        RioAcceptConnectionType::PRIVATE_CONNECTION => 'プロフィールページを非公開にし、つながり申請を受け付けない',
    ],
    AcceptConnectionByNeoType::class => [
        AcceptConnectionByNeoType::ACCEPT_APPLICATION => '参加しているNEOのRIOから申請を受け付ける',
        AcceptConnectionByNeoType::ACCEPT_CONNECTION => '所属NEOにつながりのあるNEOのメンバーからのつながり申請を受け付ける',
        AcceptConnectionByNeoType::UNSELECTED => '未選択',
    ],
    AcceptConnectionType::class => [
        AcceptConnectionType::ACCEPT_APPLICATION => '全RIOからのつながり申請を受け付ける',
        AcceptConnectionType::ACCEPT_CONNECTION => 'つながりのつながりまでのRIOからの申請を受け付ける',
        AcceptConnectionType::PRIVATE_CONNECTION => 'プロフィールページを非公開にし、つながり申請を受け付けない',
    ],
    AcceptBelongType::class => [
        AcceptBelongType::ACCEPT_APPLICATION => '参加しているNEOのRIOから申請を受け付ける',
        AcceptBelongType::ACCEPT_CONNECTION => '所属NEOにつながりのあるNEOのメンバーからのつながり申請を受け付ける',
        AcceptBelongType::UNSELECTED => '未選択',
    ],
    RoleType::class => [
        RoleType::OWNER => 'オーナー',
        RoleType::ADMINISTRATOR => '管理者',
        RoleType::MEMBER => 'メンバー',
    ],
    YearsOfExperiences::class => [
        YearsOfExperiences::LESS_ONE => '1年未満',
        YearsOfExperiences::ONE_TO_THREE => '1年以上~3年未満',
        YearsOfExperiences::THREE_TO_FIVE => '3年以上5年未満',
        YearsOfExperiences::FIVE_TO_TEN => '5年以上10年未満',
        YearsOfExperiences::OVER_TEN => '10年以上',
    ],
    DisplayType::class => [
        DisplayType::HIDE => '非公開',
        DisplayType::DISPLAY => '公開',
    ],
    RestrictConnectionType::class => [
        RestrictConnectionType::ACCEPT_CONNECTION => '全RIO・NEOからのつながり申請を受付',
        RestrictConnectionType::DO_NOT_ACCEPT_CONNECTION => 'つながり申請を受け付けない',
    ],
    AcceptParticipationType::class => [
        AcceptParticipationType::ACCEPT_PARTICIPATION => '全RIOからの参加申請を受付',
        AcceptParticipationType::DO_NOT_ACCEPT_PARTICIPATION => '参加申請を受け付けない',
    ],
    ListFilters::class => [
        ListFilters::SHOW_ALL => '全て表示',
        ListFilters::ONLY_RIO => 'RIOのみ表示',
        ListFilters::ONLY_NEO => 'NEOのみ表示',
    ],
    OverseasSupportType::class => [
        OverseasSupportType::NO => '無',
        OverseasSupportType::YES => '有',
    ],
    SaleProductVisibility::class => [
        SaleProductVisibility::IS_PRIVATE => '非公開',
        SaleProductVisibility::IS_PUBLIC => '公開',
    ],
    SaleProductAccessibility::class => [
        SaleProductAccessibility::CLOSED => '受付中止',
        SaleProductAccessibility::OPEN => '受付中',
    ],
    MessageSender::class => [
        MessageSender::SELLER => '売り手側メッセージ',
        MessageSender::BUYER => '買い手側メッセージ',
    ],
    PaymentMethods::class => [
        PaymentMethods::CARD => 'カード',
        PaymentMethods::TRANSFER => '銀行振込',
    ],
    PaymentStatuses::class => [
        PaymentStatuses::PENDING => '決済待ち',
        PaymentStatuses::AUTOMATICALLY_COMPLETED => '決済完了(自動反映)',
        PaymentStatuses::MANUALLY_COMPLETED => '決済完了(手動反映)',
    ],
    SettingTypes::class => [
        SettingTypes::SAVINGS => '普',
        SettingTypes::CURRENT => '当',
    ],
    Types::class => [
        Types::QUOTATION => '見積書',
        Types::PURCHASE_ORDER => '発注書',
        Types::DELIVERY_SLIP => '納品書',
        Types::INVOICE => '請求書',
        Types::RECEIPT => '領収書',
    ],
    ProductTaxDistinction::class => [
        ProductTaxDistinction::PERCENT_10 => '10%',
        ProductTaxDistinction::REDUCTION_8_PERCENT => '軽減8%',
        ProductTaxDistinction::NOT_APPLICABLE => '対象外',
    ],
    StatusTypes::class => [
        StatusTypes::APPLYING => '申請中',
        StatusTypes::REMANDED => '差戻中',
        StatusTypes::APPROVED => '承認完了',
        StatusTypes::REJECTED => '否認済',
        StatusTypes::CANCELLED => '申請取消',
    ],
    ApproverStatusTypes::class => [
        ApproverStatusTypes::PENDING => '対応待',
        ApproverStatusTypes::DONE => '対応済',
        ApproverStatusTypes::COMPLETED => '承認完了',
        ApproverStatusTypes::REJECTED => '申請取消',
        ApproverStatusTypes::CANCELLED => '申請取消',
    ],
    ReactionTypes::class => [
        ReactionTypes::PENDING => '対応待ち',
        ReactionTypes::APPROVED => '承認',
        ReactionTypes::RETURNED => '差戻',
        ReactionTypes::REJECTED => '否認',
    ],
];
