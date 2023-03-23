<?php

/**
 * Neo and Rio Connections - Statuses Constants
 */

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * App\Enums\ConnectionStatuses
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class ConnectionStatuses extends Enum
{
    /**
     * Pending
     *
     * @var int
     */
    public const PENDING = 0;

    /**
     * Connected
     *
     * @var int
     */
    public const CONNECTED = 1;

    /**
     * Connected
     *
     * @var int
     */
    public const APPLYING_BY_NEO = 2;
}
