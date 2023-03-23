<?php

/**
 * Neo Belongs - Statuses Constants
 */

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * App\Enums\NeoBelongStatuses
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class NeoBelongStatuses extends Enum
{
    /**
     * Pending
     *
     * @var int
     */
    public const PENDING = 0;

    /**
     * Affiliated
     *
     * @var int
     */
    public const AFFILIATED = 1;

    /**
     * Inviting
     *
     * @var int
     */
    public const INVITING = 2;

    /**
     * Declined
     *
     * @var int
     */
    public const DECLINED = 3;
}
