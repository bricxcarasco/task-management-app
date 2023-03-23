<?php

/**
 * Schedule - Guest statuses
 */

namespace App\Enums\Schedule;

use BenSampo\Enum\Enum;

/**
 * App\Enums\Schedule\GuestStatuses
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class GuestStatuses extends Enum
{
    /**
     * Waiting for response
     *
     * @var int
     */
    public const WAITING_FOR_RESPONSE = 0;

    /**
     * Participate
     *
     * @var int
     */
    public const PARTICIPATE = 1;

    /**
     * Not participate
     *
     * @var int
     */
    public const NOT_PARTICIPATE = -1;
}
