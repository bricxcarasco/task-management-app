<?php

/**
 * Form Types
 */

namespace App\Enums\Form;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

/**
 * App\Enums\Form\Types
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class Types extends Enum implements LocalizedEnum
{
    /**
     * Form Type - Quotation
     *
     * @var int
     */
    public const QUOTATION = 1;

    /**
     * Form Type - Purchase Order
     *
     * @var int
     */
    public const PURCHASE_ORDER = 2;

    /**
     * Form Type - Delivery Slip
     *
     * @var int
     */
    public const DELIVERY_SLIP = 3;

    /**
     * Form Type - Invoice
     *
     * @var int
     */
    public const INVOICE = 4;

    /**
     * Form Type - Receipt
     *
     * @var int
     */
    public const RECEIPT = 5;
}
