<?php

/**
 * PaidPlan - PaymentMethod types
 */

namespace App\Enums\PaidPlan;

use BenSampo\Enum\Enum;

/**
 * App\Enums\PaidPlan\PaymentMethod
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class PaymentMethodType extends Enum
{
    /**
     * CC/Stripe
     *
     * @var int
     */
    public const STRIPE = 1;

    /**
     *  Bank Transfer
     *
     * @var int
     */
    public const BANK_TRANSFER = 2;
}
