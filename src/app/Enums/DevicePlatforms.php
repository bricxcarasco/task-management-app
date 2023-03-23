<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * App\Enums\DevicePlatforms
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class DevicePlatforms extends Enum
{
    /**
     * Android
     *
     * @var string
     */
    public const ANDROID = 'Android';

    /**
     * iOS
     *
     * @var string
     */
    public const IOS = 'iOS';

    /**
     * Other
     *
     * @var string
     */
    public const OTHER = 'Others';
}
