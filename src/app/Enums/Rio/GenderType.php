<?php

/**
 * Gender Constant Values
 */

namespace App\Enums\Rio;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Contracts\LocalizedEnum;

/**
 * App\Enums\Rio\GenderType
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class GenderType extends Enum implements LocalizedEnum
{
    /**
     * Male
     *
     * @var int
     */
    public const MALE = 1;

    /**
     * Female
     *
     * @var int
     */
    public const FEMALE = 2;
}
