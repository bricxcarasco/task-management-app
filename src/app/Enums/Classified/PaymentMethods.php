<?php

/**
 * Payment Methods
 */

namespace App\Enums\Classified;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

/**
 * App\Enums\PaymentMethods
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class PaymentMethods extends Enum implements LocalizedEnum
{
    /**
     * Payment method via Card payment
     *
     * @var string
     */
    public const CARD = 'card';

    /**
     * Payment method via Bank transfer
     *
     * @var string
     */
    public const TRANSFER = 'transfer';
}
