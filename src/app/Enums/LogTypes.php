<?php

/**
 * RIO/NEO Log types
 */

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * App\Enums\LogTypes
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class LogTypes extends Enum
{
    /**
     * Log type of viewing a profile page
     *
     * @var int
     */
    public const VIEWED = 1;

    /**
     * Log type of accepting a group invitation
     *
     * @var int
     */
    public const INVITE_ACCEPTED = 2;
}
