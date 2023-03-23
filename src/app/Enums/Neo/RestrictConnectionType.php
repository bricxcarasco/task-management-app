<?php

namespace App\Enums\Neo;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Contracts\LocalizedEnum;

/**
 * App\Enums\Neo\RestrictConnectionType
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class RestrictConnectionType extends Enum implements LocalizedEnum
{
    /**
    * Accepts connection applications from all RIO / NEO
    *
    * @var int
    */
    public const ACCEPT_CONNECTION = 1;

    /**
    *  Do not accept connection applications
    *
    * @var int
    */
    public const DO_NOT_ACCEPT_CONNECTION = 2;
}
