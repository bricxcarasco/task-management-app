<?php

/**
 * Payment Statuses
 */

namespace App\Enums\Classified;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

/**
 * App\Enums\PaymentStatuses
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class PaymentStatuses extends Enum implements LocalizedEnum
{
    /**
     * Payment status - Pending
     *
     * @var int
     */
    public const PENDING = 0;

    /**
     * Payment status - Automatically Completed
     *
     * @var int
     */
    public const AUTOMATICALLY_COMPLETED = 1;

    /**
     * Payment status - Manually Completed
     *
     * @var int
     */
    public const MANUALLY_COMPLETED = 2;
}
