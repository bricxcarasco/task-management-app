<?php

/**
 * Notification Announcement Status
 */

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * App\Enums\NotificationStatus
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class NotificationTypes extends Enum
{
    /**
     * Pending
     *
     * @var int
     */
    public const PERSONAL = 0;

    /**
     * Notification can be for public notice
     *
     * @var int
     */
    public const FOR_ANNOUNCEMENT = 1;
}
