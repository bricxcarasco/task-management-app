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
use App\Enums\Workflow\SortType;

return [
    BasicSettings::class => [
        BasicSettings::CONNECTION_APPLICATION => 'Connection application',
        BasicSettings::CONNECTION_GROUP_INVITATION => 'Connection group invitation',
        BasicSettings::NEO_INVITATION => 'NEO invitation',
        BasicSettings::CHAT_MESSAGE => 'Chat messages',
        BasicSettings::ADD_DOCUMENT_SHARING => 'Add sharing to document management',
        BasicSettings::SCHEDULE_INVITATION => 'Schedule invitation',
        BasicSettings::CHANGE_EMAIL => 'Change email',
        BasicSettings::CHANGE_PASSWORD => 'Change password',
    ],
    ChatTypes::class => [
        ChatTypes::CONNECTED => 'Connected chat',
        ChatTypes::CONNECTED_GROUP => 'Connected group chat',
        ChatTypes::NEO_GROUP => 'Group chat within NEO',
        ChatTypes::NEO_MESSAGE => 'NEO message delivery',
    ],
    GenderType::class => [
        GenderType::MALE => 'Male',
        GenderType::FEMALE => 'Female',
    ],
    GroupStatuses::class => [
        GroupStatuses::REQUESTING => 'In process of application / requesting',
        GroupStatuses::CONNECTED => 'Connected state',
    ],
    PrefectureTypes::class => [
        PrefectureTypes::OTHER => 'Other',
        PrefectureTypes::HOKKAIDO => 'Hokkaido',
        PrefectureTypes::AOMORI => 'Aomori',
        PrefectureTypes::IWATE => 'Iwate',
        PrefectureTypes::MIYAGI => 'Miyagi',
        PrefectureTypes::AKITA => 'Akita',
        PrefectureTypes::YAMAGATA => 'Yamagata',
        PrefectureTypes::FUKUSHIMA => 'Fukushima',
        PrefectureTypes::IBARAKI => 'Ibaraki',
        PrefectureTypes::TOCHIGI => 'Tochigi',
        PrefectureTypes::GUNMA => 'Gunma',
        PrefectureTypes::SAITAMA => 'Saitama',
        PrefectureTypes::CHIBA => 'Chiba',
        PrefectureTypes::TOKYO => 'Tokyo',
        PrefectureTypes::KANAGAWA => 'Kanagawa',
        PrefectureTypes::NIIGATA => 'Niigata',
        PrefectureTypes::TOYAMA => 'Toyama',
        PrefectureTypes::ISHIKAWA => 'Ishikawa',
        PrefectureTypes::FUKUI => 'Fukui',
        PrefectureTypes::YAMANASHI => 'Yamanashi',
        PrefectureTypes::NAGANO => 'Nagano',
        PrefectureTypes::GIFU => 'Gifu',
        PrefectureTypes::SHIZUOKA => 'Shizuoka',
        PrefectureTypes::AICHI => 'Aichi',
        PrefectureTypes::MIE => 'Mie',
        PrefectureTypes::SHIGA => 'Shiga',
        PrefectureTypes::KYOTO => 'Kyoto',
        PrefectureTypes::OSAKA => 'Osaka',
        PrefectureTypes::HYOGO => 'Hyogo',
        PrefectureTypes::NARA => 'Nara',
        PrefectureTypes::WAKAYAMA => 'Wakayama',
        PrefectureTypes::TOTTORI => 'Tottori',
        PrefectureTypes::SHIMANE => 'Shimane',
        PrefectureTypes::OKAYAMA => 'Okayama',
        PrefectureTypes::HIROSHIMA => 'Hiroshima',
        PrefectureTypes::YAMAGUCHI => 'Yamaguchi',
        PrefectureTypes::TOKUSHIMA => 'Tokushima',
        PrefectureTypes::KAGAWA => 'Kagawa',
        PrefectureTypes::EHIME => 'Ehime',
        PrefectureTypes::KOCHI => 'Kochi',
        PrefectureTypes::FUKUOKA => 'Fukuoka',
        PrefectureTypes::SAGA => 'Saga',
        PrefectureTypes::NAGASAKI => 'Nagasaki',
        PrefectureTypes::KUMAMOTO => 'Kumamoto',
        PrefectureTypes::OITA => 'Oita',
        PrefectureTypes::MIYAZAKI => 'Miyazaki',
        PrefectureTypes::KAGOSHIMA => 'Kagoshima',
        PrefectureTypes::OKINAWA => 'Okinawa',
    ],
    Priorities::class => [
        Priorities::LOW => 'Low priority',
        Priorities::MID => 'Mid priority',
        Priorities::HIGH => 'High priority',
    ],
    SecretQuestionType::class => [
        SecretQuestionType::FIRST_QUESTION => 'What is the name of the town your parents met?',
        SecretQuestionType::SECOND_QUESTION => 'What is your boss name at work for the first time?',
        SecretQuestionType::THIRD_QUESTION => 'What is the name of the town where you spent your childhood?',
    ],
    OrganizationAttributeType::class => [
        OrganizationAttributeType::CORPORATE => 'Corporate',
        OrganizationAttributeType::NPO => 'NPO',
        OrganizationAttributeType::SCHOOL => 'School',
        OrganizationAttributeType::ALUMNI => 'Alumni',
    ],
    RioAcceptConnectionType::class => [
        RioAcceptConnectionType::ACCEPT_APPLICATION => 'Accept connection applications from all RIOs',
        RioAcceptConnectionType::ACCEPT_CONNECTION => 'Accept applications from RIO up to the connection',
        RioAcceptConnectionType::PRIVATE_CONNECTION => 'Keep your profile page private and do not accept connection requests',
    ],
    AcceptConnectionByNeoType::class => [
        AcceptConnectionByNeoType::ACCEPT_APPLICATION => 'Accept applications from participating NEO\'s RIO',
        AcceptConnectionByNeoType::ACCEPT_CONNECTION => 'Accept connection requests from NEO members who are connected to your NEO (default)',
        AcceptConnectionByNeoType::UNSELECTED => 'Unselected',
    ],
    AcceptConnectionType::class => [
        AcceptConnectionType::ACCEPT_APPLICATION => 'Accept connection applications from all RIOs',
        AcceptConnectionType::ACCEPT_CONNECTION => 'Accept applications from RIO up to the connection',
        AcceptConnectionType::PRIVATE_CONNECTION => 'Keep your profile page private and do not accept connection requests',
    ],
    AcceptBelongType::class => [
        AcceptBelongType::ACCEPT_APPLICATION => 'Accept applications from participating NEO\'s RIO',
        AcceptBelongType::ACCEPT_CONNECTION => 'Accept connection requests from NEO members who are connected to your NEO (default)',
        AcceptBelongType::UNSELECTED => 'Unselected',
    ],
    RoleType::class => [
        RoleType::OWNER => 'Owner',
        RoleType::ADMINISTRATOR => 'Administrator',
        RoleType::MEMBER => 'Member',
    ],
    YearsOfExperiences::class => [
        YearsOfExperiences::LESS_ONE => 'Less than a year',
        YearsOfExperiences::ONE_TO_THREE => '1 year or more ~ less than 3 years',
        YearsOfExperiences::THREE_TO_FIVE => '3 years or more ~ less than 5 years',
        YearsOfExperiences::FIVE_TO_TEN => '5 years or more ~ less than 10 years',
        YearsOfExperiences::OVER_TEN => 'Over 10 years',
    ],
    DisplayType::class => [
        DisplayType::HIDE => 'Hide',
        DisplayType::DISPLAY => 'Display',
    ],
    RestrictConnectionType::class => [
        RestrictConnectionType::ACCEPT_CONNECTION => 'Accepts connection applications from all RIO / NEO',
        RestrictConnectionType::DO_NOT_ACCEPT_CONNECTION => 'Do not accept connection applications',
    ],
    AcceptParticipationType::class => [
        AcceptParticipationType::ACCEPT_PARTICIPATION => ' Accepting participation applications from all RIOs',
        AcceptParticipationType::DO_NOT_ACCEPT_PARTICIPATION => 'Do not accept participation application',
    ],
    ListFilters::class => [
        ListFilters::SHOW_ALL => 'Show All',
        ListFilters::ONLY_RIO => 'RIO Only',
        ListFilters::ONLY_NEO => 'NEO Only',
    ],
    OverseasSupportType::class => [
        OverseasSupportType::NO => 'NO',
        OverseasSupportType::YES => 'YES',
    ],
    SaleProductVisibility::class => [
        SaleProductVisibility::IS_PRIVATE => 'Private',
        SaleProductVisibility::IS_PUBLIC => 'Public',
    ],
    SaleProductAccessibility::class => [
        SaleProductAccessibility::CLOSED => 'Closed',
        SaleProductAccessibility::OPEN => 'Open',
    ],
    MessageSender::class => [
        MessageSender::SELLER => "Seller's message",
        MessageSender::BUYER => "Buyer's message",
    ],
    PaymentMethods::class => [
        PaymentMethods::CARD => 'Card',
        PaymentMethods::TRANSFER => 'Bank transfer',
    ],
    PaymentStatuses::class => [
        PaymentStatuses::PENDING => 'The decision is pending',
        PaymentStatuses::AUTOMATICALLY_COMPLETED => 'The decision is completed (automatically reflected)',
        PaymentStatuses::MANUALLY_COMPLETED => 'The decision is completed (manually reflected)',
    ],
    SettingTypes::class => [
        SettingTypes::SAVINGS => 'Savings',
        SettingTypes::CURRENT => 'Current',
    ],
    Types::class => [
        Types::class => 'Quotation',
        Types::class => 'Purchase Order',
        Types::class => 'Delivery Slip',
        Types::class => 'Invoice',
        Types::class => 'Receipt',
    ],
    ProductTaxDistinction::class => [
        ProductTaxDistinction::PERCENT_10 => '10%',
        ProductTaxDistinction::REDUCTION_8_PERCENT => 'Reduction 8%',
        ProductTaxDistinction::NOT_APPLICABLE => 'Not Applicable',
    ],
    StatusTypes::class => [
        StatusTypes::APPLYING => 'Applying',
        StatusTypes::REMANDED => 'Sending back',
        StatusTypes::APPROVED => 'Approval completed',
        StatusTypes::REJECTED => 'No settlement',
        StatusTypes::CANCELLED => 'Application cancelled',
    ],
    ApproverStatusTypes::class => [
        ApproverStatusTypes::PENDING => 'Waiting',
        ApproverStatusTypes::DONE => 'Done',
        ApproverStatusTypes::COMPLETED => 'Approval completed',
        ApproverStatusTypes::REJECTED => 'No settlement',
        ApproverStatusTypes::CANCELLED => 'Application cancelled',
    ],
    ReactionTypes::class => [
        ReactionTypes::PENDING => 'Pending',
        ReactionTypes::APPROVED => 'Sending Approved',
        ReactionTypes::RETURNED => 'Returned',
        ReactionTypes::REJECTED => 'Denied',
    ],
    SortTypes::class => [
        SortType::NEWEST_APPLICATION_DATE => 'Newest application date',
        SortType::OLDEST_APPLICATION_DATE => 'Oldest application date'
    ],
];
