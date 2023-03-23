<?php

/**
 * OTP Type Constants
 */

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * App\Enums\OTPTypes
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class OTPTypes extends Enum
{
    /**
     * SMS OTP
     *
     * @var int
     */
    public const SMS = 0;

    /**
     * Voice OTP
     *
     * @var int
     */
    public const VOICE = 1;
}
