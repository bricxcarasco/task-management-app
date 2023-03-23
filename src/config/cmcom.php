<?php

return [
    /*
    |--------------------------------------------------------------------------
    | CM.com API Host Endpoint
    |--------------------------------------------------------------------------
    |
    */

    'host' => env('CMCOM_API_HOST', 'https://api.cm.com'),

    /*
    |--------------------------------------------------------------------------
    | CM.com - Message Gateway API Key
    |--------------------------------------------------------------------------
    |
    */

    'message_api_key' => env('CMCOM_MESSAGE_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | CM.com - Voice API - API Key
    |--------------------------------------------------------------------------
    |
    */

    'voice_api_key' => env('CMCOM_VOICE_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | CM.com - Message Gateway Sender Name
    |--------------------------------------------------------------------------
    |
    */

    'message_gateway_sender' => env('CMCOM_MESSAGE_GATEWAY_SENDER', 'HERO'),

    /*
    |--------------------------------------------------------------------------
    | CM.com - Voice API - Configuration
    |--------------------------------------------------------------------------
    |
    */

    'voice_api' => [
        'caller' => env('CMCOM_VOICE_CALLER_NUMBER'),
        'anonymous' => env('CMCOM_VOICE_ANONYMOUS', true),
        'max_replays' => env('CMCOM_VOICE_MAX_REPLAYS', 1),
        'auto_replay' => env('CMCOM_VOICE_AUTO_REPLAY', true),
        'operator' => [
            'language' => env('CMCOM_VOICE_OPERATOR_LANGUAGE', 'ja-JP'),
            'gender' => env('CMCOM_VOICE_OPERATOR_GENDER', 'Female'),
            'version' => env('CMCOM_VOICE_OPERATOR_VERSION', 1),
            'volume' => env('CMCOM_VOICE_OPERATOR_VOLUME', 3),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | CM.com - CM Sign API Host Endpoint
    |--------------------------------------------------------------------------
    |
    */

    'sign_service_host' => 'https://api.cmdisp.com',
    'sign_service_host_sandbox' => 'https://api.sandbox.cmdisp.com',

    /*
    |--------------------------------------------------------------------------
    | CM.com - CM Sign API Key
    |--------------------------------------------------------------------------
    |
    */

    'sign_service_api_key' => env('CMCOM_SIGN_API_KEY'),
    'sign_service_api_key_sandbox' => env('CMCOM_SIGN_API_KEY_SANDBOX'),

    /*
    |--------------------------------------------------------------------------
    | CM.com - CM Sign API Secret
    |--------------------------------------------------------------------------
    |
    */

    'sign_service_secret' => env('CMCOM_SIGN_SECRET'),
    'sign_service_secret_sandbox' => env('CMCOM_SIGN_SECRET_SANDBOX'),

    /*
    |--------------------------------------------------------------------------
    | CM Sign signature key - Serves as a key to recognize that the requester is from CM Sign
    |--------------------------------------------------------------------------
    |
    */

    'sign_webhook_key' => env('CM_SIGN_WEBHOOK_SIGNATURE'),
    'sign_webhook_key_sandbox' => env('CM_SIGN_WEBHOOK_SIGNATURE_SANDBOX'),

    /*
    |--------------------------------------------------------------------------
    | CM Sign signature key - Serves as a key to recognize that the requester is from CM Sign
    |--------------------------------------------------------------------------
    |
    */

    'sign_env' => env('CMCOM_SIGN_ENV'),

    /*
    |--------------------------------------------------------------------------
    | CM Sign Service Configuration
    |--------------------------------------------------------------------------
    |
    */

    'sign_service' => [
        'locale' => env('CMCOM_SIGN_LOCALE', 'ja-JP'),
    ],

];
