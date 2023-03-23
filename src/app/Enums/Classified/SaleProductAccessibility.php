<?php

/**
 * Sale Product Accessibility
 */

namespace App\Enums\Classified;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

/**
 * App\Enums\SaleProductAccessibility
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class SaleProductAccessibility extends Enum implements LocalizedEnum
{
    /**
     * Product is Closed
     *
     * @var int
     */
    public const CLOSED = 0;

    /**
     * Product is Open
     *
     * @var int
     */
    public const OPEN = 1;
}
