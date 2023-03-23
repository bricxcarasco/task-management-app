<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode
    |--------------------------------------------------------------------------
    |
    | Custom configuration for maintenance mode
    |
    */

    'maintenance' => [
        'secret' => env('BPHERO_MAINTENANCE_SECRET', 'ithero-mnt'),
        'refresh' => env('BPHERO_MAINTENANCE_REFRESH'),
        'render' => env('BPHERO_MAINTENANCE_RENDER', 'errors::maintenance'),
        'redirect' => env('BPHERO_MAINTENANCE_REDIRECT', '/'),
        'schedule' => env('BPHERO_MAINTENANCE_SCHEDULE'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Reference URL
    |--------------------------------------------------------------------------
    |
    */

    'reference_url' => 'https://hero.com/ec/',

    /*
    |--------------------------------------------------------------------------
    | Service Selected Cookie Config
    |--------------------------------------------------------------------------
    |
    */

    'service_selected_cookie_key' =>  env('BPHERO_SERVICE_SELECTED_COOKIE_KEY', 'bphero_service_selected'),
    'service_selected_cookie_expiration' =>  env('BPHERO_SERVICE_SELECTED_COOKIE_EXPIRATION', 60 * 24 * 30),

    /*
    |--------------------------------------------------------------------------
    | Email Verification Token Length
    |--------------------------------------------------------------------------
    |
    |  Used for generating tokens in user_verifications table
    |
    */

    'email_verification_token_length' => 40,

    /*
    |--------------------------------------------------------------------------
    | SMS Authentication Configuration
    |--------------------------------------------------------------------------
    |
    */

    'sms_auth_code_length' => env('BPHERO_SMS_AUTH_CODE_LENGTH', 4),
    'sms_auth_code_expiry' => env('BPHERO_SMS_AUTH_CODE_EXPIRY', 600),
    'sms_auth_bypass_flag' => env('BPHERO_SMS_AUTH_BYPASS_FLAG', false),

    /*
    |--------------------------------------------------------------------------
    | CRONTAB Checker
    |--------------------------------------------------------------------------
    |
    */

    'is_executing_crontab' => env('BPHERO_CRONTAB_CHECKER', false),

    /*
    |--------------------------------------------------------------------------
    | Paginate Count
    |--------------------------------------------------------------------------
    |
    */

    'paginate_count' => 20,

    /*
    |--------------------------------------------------------------------------
    | Profile image
    |--------------------------------------------------------------------------
    |
    */

    'profile_image_filename' => 'user_default.png',
    'profile_image_directory' => 'images/profile/',

    /*
    |--------------------------------------------------------------------------
    | Product limit
    |--------------------------------------------------------------------------
    |
    */

    'product_limit' => 10,

    /*
    |--------------------------------------------------------------------------
    | Group Connection Maximum Invitee/Participants
    |--------------------------------------------------------------------------
    |
    */

    'group_connection_maximum_invitee' => 5,

    /*
    |--------------------------------------------------------------------------
    | Referral Code Lenth
    |--------------------------------------------------------------------------
    |
    */

    'referral_code_length' => 16,

    /*
    |--------------------------------------------------------------------------
    | Document storage path
    |--------------------------------------------------------------------------
    |
    */

    'rio_document_storage_path' => 'documents/rio/',
    'neo_document_storage_path' => 'documents/neo/',
    'document_storage_path' => 'http://bphero.ynsdev.pw/',

    /*
    |--------------------------------------------------------------------------
    | Classified Sales Configuration
    |--------------------------------------------------------------------------
    |
    */

    'rio_classified_storage_path' => 'classifieds/rio/',
    'neo_classified_storage_path' => 'classifieds/neo/',
    'messages_storage_path' => 'classifieds/messages/',
    'default_product_image' => 'around/img/80.png',
    'classified_sales_max_files' => 5,
    'classified_sales_max_file_size' => '5MB',
    'missing_requirements_max_count' => 10,


   /*
    |--------------------------------------------------------------------------
    | Form Configuration
    |--------------------------------------------------------------------------
    |
    */

    'form_service_disk' => 'public',
    'form_max_file_size' => '5MB',
    'form_max_files' => 1,
    'rio_basic_settings_storage_path' => 'basic_settings/rio/',
    'neo_basic_settings_storage_path' => 'basic_settings/neo/',

    /*
    |--------------------------------------------------------------------------
    | Form Configuration
    |--------------------------------------------------------------------------
    |
    */

    'form_service_disk' => 'public',
    'form_max_file_size' => '5MB',
    'form_max_files' => 1,
    'rio_basic_settings_storage_path' => 'basic_settings/rio/',
    'neo_basic_settings_storage_path' => 'basic_settings/neo/',

    /*
    |--------------------------------------------------------------------------
    | Payment Access Key Length
    |--------------------------------------------------------------------------
    |
    */

    'payment_access_key_length' => env('PAYMENT_ACCESS_KEY_LENGTH', 50),

    /*
    |--------------------------------------------------------------------------
    | S3 filesystem names
    |--------------------------------------------------------------------------
    |
    */

    'public_bucket' => 'public_s3',
    'private_bucket' => 'private_s3',

    /*
    |--------------------------------------------------------------------------
    | S3 bucket directory names
    |--------------------------------------------------------------------------
    |
    */

    'public_directory' => 'public',
    'private_directory' => 'private',

    /*
    |--------------------------------------------------------------------------
    | Temp Directory
    |--------------------------------------------------------------------------
    |
    */

    'temp_upload_directory' => 'temp/upload/',
    'temp_download_directory' => 'temp/download/',

    /*
    |--------------------------------------------------------------------------
    | Chat text limit | Ellipsis
    |--------------------------------------------------------------------------
    |
    */
    'chat_text_limit' => 25,
    'inquiry_text_limit' => 25,

    /*
    |--------------------------------------------------------------------------
    | Notification dropdown menu limit
    |--------------------------------------------------------------------------
    |
    */

    'notification_dropdown_limit' => 5,

    /*
    |--------------------------------------------------------------------------
    | Profile image directory
    |--------------------------------------------------------------------------
    |
    */

    'rio_profile_image' => 'profile_image/rio/',
    'neo_profile_image' => 'profile_image/neo/',
    'profile_image_storage_path' => 'storage/profile_image/',
    'basic_settings_storage_path' => 'storage/basic_settings/',

    /*
    |--------------------------------------------------------------------------
    | CM Sign token Configuration
    |--------------------------------------------------------------------------
    |
    */

    'electronic_contract_free_slots' => env('ELECTRONIC_CONTRACT_FREE_SLOTS', 20),
    'electronic_contract_expiration_date' => env('ELECTRONIC_CONTRACT_EXPIRATION_DATE', '2022/12/01'),

    /*
    |--------------------------------------------------------------------------
    | Maximum bank account
    |--------------------------------------------------------------------------
    |
    */

    'max_bank_account' => 3,

    /*
    |--------------------------------------------------------------------------
    | Electronic contracts configuration
    |--------------------------------------------------------------------------
    |
    */

    'cmcom_sign_jwt_expiration_duration' => env('BPHERO_CMCOM_SIGN_JWT_EXPIRATION', '+15 mins'),
    'cmcom_sign_contract_sign_expiration' => env('BPHERO_CMCOM_CONTRACT_SIGN_EXPIRATION', 2592000),
    'cmcom_sign_bypass_flag' => env('BPHERO_CMCOM_SIGN_BYPASS_FLAG', false),

    /*
    |--------------------------------------------------------------------------
    | Forms issuer image
    |--------------------------------------------------------------------------
    |
    */

    'form_issuer_image' => 'forms/issuer/profile_image',

    /*
    |--------------------------------------------------------------------------
    | Forms history issuer image
    |--------------------------------------------------------------------------
    |
    */

    'form_histories_issuer_image' => 'form_histories/issuer/profile_image/',

    /*
    |--------------------------------------------------------------------------
    | Conversion value from GB to Bytes
    |--------------------------------------------------------------------------
    |
    */

    'gb_to_bytes' => 1073741824,

    /*
    |--------------------------------------------------------------------------
    | Talk Subject Cookie Config
    |--------------------------------------------------------------------------
    |
    */

    'talk_subject_cookie_key' =>  env('BPHERO_TALK_SUBJECT_COOKIE_KEY', 'bphero_talk_subject'),
    'talk_subject_cookie_expiration' =>  env('BPHERO_TALK_SUBJECT_COOKIE_EXPIRATION', 60 * 24 * 30),
];
