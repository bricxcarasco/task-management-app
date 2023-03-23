<?php

namespace App\Enums\Neo;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Contracts\LocalizedEnum;

/**
 * App\Enums\Neo\RoleType
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class RoleType extends Enum implements LocalizedEnum
{
    /**
    * Neo belongs role owner
    *
    * @var int
    */
    public const OWNER = 1;

    /**
    * Neo belongs role administrator
    *
    * @var int
    */
    public const ADMINISTRATOR = 2;

    /**
    * Neo belongs role member
    *
    * @var int
    */
    public const MEMBER = 3;
}
