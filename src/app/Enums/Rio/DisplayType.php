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
final class DisplayType extends Enum implements LocalizedEnum
{
    /**
     * PRIVATE
     *
     * @var int
     */
    public const HIDE = 0;

    /**
     * PUBLIC
     *
     * @var int
     */
    public const DISPLAY = 1;
}
