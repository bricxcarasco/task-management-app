<?php

/**
 * Form Product Tax Distinction
 */

namespace App\Enums\Form;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

/**
 * App\Enums\Form\ProductTaxDistinction
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class ProductTaxDistinction extends Enum implements LocalizedEnum
{
    /**
     * Tax Distinction - 10%
     *
     * @var int
     */
    public const PERCENT_10 = 1;

    /**
     * Tax Distinction - Reduction 8%
     *
     * @var int
     */
    public const REDUCTION_8_PERCENT = 2;

    /**
     * Tax Distinction - Not Applicable
     *
     * @var int
     */
    public const NOT_APPLICABLE = 3;
}
