<?php

namespace App\Enums\Connection;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Contracts\LocalizedEnum;

/**
 * App\Enums\Connection\ConnectionStatuses
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class ConnectionStatuses extends Enum implements LocalizedEnum
{
    /**
     * In process of application / requesting
     * 申請中
     *
     * @var int
     */
    public const REQUESTING = 0;

    /**
     * Connected state
     * つながり状態
     *
     * @var int
     */
    public const CONNECTED = 1;
}
