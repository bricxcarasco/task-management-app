<?php

namespace App\Enums\Neo;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Contracts\LocalizedEnum;

/**
 * App\Enums\Neo\AcceptParticipationType
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class AcceptParticipationType extends Enum implements LocalizedEnum
{
    /**
    * Accepting participation applications from all RIOs
    *
    * @var int
    */
    public const ACCEPT_PARTICIPATION = 1;

    /**
    *  Do not accept participation application
    *
    * @var int
    */
    public const DO_NOT_ACCEPT_PARTICIPATION = 2;
}
