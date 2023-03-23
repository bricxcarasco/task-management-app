<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * App\Enums\ConnectionStatusType
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class ConnectionStatusType extends Enum
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
}
