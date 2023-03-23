<?php

/**
 * Overseas support constant values
 */

namespace App\Enums\Neo;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Contracts\LocalizedEnum;

/**
 * App\Enums\Neo\OverseasSupportType
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class OverseasSupportType extends Enum implements LocalizedEnum
{
    /**
     * No
     *
     * @var int
     */
    public const NO = 0;

    /**
     * Yes
     *
     * @var int
     */
    public const YES = 1;
}
