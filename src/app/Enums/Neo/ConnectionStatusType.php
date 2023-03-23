<?php

namespace App\Enums\Neo;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Contracts\LocalizedEnum;

/**
 * App\Enums\Neo\ConnectionStatusType
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class ConnectionStatusType extends Enum implements LocalizedEnum
{
    /**
    * Connection RIO/NEO Applying status
    *
    * @var int
    */
    public const APPLYING = 0;

    /**
    * Connection RIO/NEO Connection status
    *
    * @var int
    */
    public const CONNECTION_STATUS = 1;
}
