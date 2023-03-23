<?php

return [

    /*
    |--------------------------------------------------------------------------
    | FCM Server-side Configuration
    |--------------------------------------------------------------------------
    |
    */

    'cookie_key' => env('FCM_COOKIE_KEY', 'fcm_current_token'),
    'cookie_expiration' =>  env('FCM_COOKIE_EXPIRATION', 2147483647),

];
