<?php

namespace App\Enums\Rio;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Contracts\LocalizedEnum;

/**
 * App\Enums\Rio\AcceptConnectionByNeoType
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class AcceptConnectionByNeoType extends Enum implements LocalizedEnum
{
    /**
    * Accept applications from participating NEO's RIO
    *
    * @var int
    */
    public const ACCEPT_APPLICATION = 1;

    /**
    * Accept connection requests from NEO members who are connected to your NEO (default)
    *
    * @var int
    */
    public const ACCEPT_CONNECTION = 2;

    /**
    * Unselected
    *
    * @var int
    */
    public const UNSELECTED = 3;
}
