<?php

/**
 * Years of Experience Constants
 */

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

/**
 * App\Enums\YearsOfExperiences
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class YearsOfExperiences extends Enum implements LocalizedEnum
{
    /**
     * Less than a year
     *
     * @var int
     */
    public const LESS_ONE = 1;

    /**
     * One year or more ~ Less than three years
     *
     * @var int
     */
    public const ONE_TO_THREE = 2;

    /**
     * Three years or more ~ Less than five years
     * チーム長
     *
     * @var int
     */
    public const THREE_TO_FIVE = 3;

    /**
     * Five years or more ~ Less than ten years
     *
     * @var int
     */
    public const FIVE_TO_TEN = 4;

    /**
     * Over ten years
     *
     * @var int
     */
    public const OVER_TEN = 5;
}
