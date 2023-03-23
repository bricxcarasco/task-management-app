<?php

/**
 * Sale Product Visibility
 */

namespace App\Enums\Classified;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

/**
 * App\Enums\SaleProductVisibility
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class SaleProductVisibility extends Enum implements LocalizedEnum
{
    /**
     * Product is Private
     *
     * @var int
     */
    public const IS_PRIVATE = 0;

    /**
     * Product is Public
     *
     * @var int
     */
    public const IS_PUBLIC = 1;
}
