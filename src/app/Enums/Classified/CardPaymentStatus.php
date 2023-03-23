<?php

/**
 * Card Payment Status
 */

namespace App\Enums\Classified;

use BenSampo\Enum\Enum;

/**
 * App\Enums\Classified\CardPaymentStatus
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class CardPaymentStatus extends Enum
{
    /**
     * Card Payment Status - No card payment yet
     *
     * @var int
     */
    public const NO_CARD_PAYMENT = 0;

    /**
     * Card Payment Status - Restricted
     *
     * @var int
     */
    public const RESTRICTED = 1;

    /**
     * Card Payment Status - Pending
     *
     * @var int
     */
    public const PENDING = 2;

    /**
     * Card Payment Status - Completed
     *
     * @var int
     */
    public const COMPLETED = 3;
}
