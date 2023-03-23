<?php

use App\Enums\DevicePlatforms;

return [

    /*
    |--------------------------------------------------------------------------
    | Cordova Server-side Configuration
    |--------------------------------------------------------------------------
    |
    */

    'platform_key' => env('CORDOVA_PLATFORM_KEY', 'cordova_platform'),
    'platform_token' => env('CORDOVA_PLATFORM_TOKEN', 'qbuce9qJq7UbNb6adnp5WK6QIWLWsZFO'),
    'platform_type_key' => env('CORDOVA_PLATFORM_TYPE_KEY', 'cordova_platform_type'),
    'cookie_expiration' =>  env('CORDOVA_COOKIE_EXPIRATION', 2147483647),

    /*
    |--------------------------------------------------------------------------
    | Plugins - Manual Configuration
    |--------------------------------------------------------------------------
    |
    */

    'plugins' => [
        DevicePlatforms::IOS => [
            'inappbrowser',
        ],
    ]

];
