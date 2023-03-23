<?php

namespace App\Enums\Rio;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Contracts\LocalizedEnum;

/**
 * App\Enums\Rio\AcceptConnectionType
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class AcceptConnectionType extends Enum implements LocalizedEnum
{
    /**
    * Accept connection applications from all RIOs
    *
    * @var int
    */
    public const ACCEPT_APPLICATION = 1;

    /**
    * Accept applications from RIO up to the connection (default)
    *
    * @var int
    */
    public const ACCEPT_CONNECTION = 2;

    /**
    * Keep your profile page private and do not accept connection requests
    *
    * @var int
    */
    public const PRIVATE_CONNECTION = 3;
}
