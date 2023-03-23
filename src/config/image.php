<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Image Driver
    |--------------------------------------------------------------------------
    |
    | Intervention Image supports "GD Library" and "Imagick" to process images
    | internally. You may choose one of them according to your PHP
    | configuration. By default PHP's "GD Library" implementation is used.
    |
    | Supported: "gd", "imagick"
    |
    */

    'driver' => 'imagick',

    /*
    |--------------------------------------------------------------------------
    | Downsize Configuration
    |--------------------------------------------------------------------------
    |
    | Target downsize via resize
    |
    */

    'default_target_downsize' => env('IMAGE_DEFAULT_TARGET_DOWNSIZE', 200000),

];
