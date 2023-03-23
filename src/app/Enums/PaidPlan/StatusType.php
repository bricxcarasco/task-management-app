<?php

/**
 * PaidPlan - Status types
 */

namespace App\Enums\PaidPlan;

use BenSampo\Enum\Enum;

/**
 * App\Enums\PaidPlan\StatusType
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class StatusType extends Enum
{
    /**
     * Incomplete Status Type
     *
     * @var int
     */
    public const INCOMPLETE = 0;

    /**
     * Incomplete Expired Status Type
     *
     * @var int
     */
    public const INCOMPLETE_EXPIRED = 1;

    /**
     * Trialing Status Type
     *
     * @var int
     */
    public const TRIALING = 2;

    /**
     * Active Status Type
     *
     * @var int
     */
    public const ACTIVE = 3;

    /**
     * Past Due Status Type
     *
     * @var int
     */
    public const PAST_DUE = 4;

    /**
     * Cancelled Status Type
     *
     * @var int
     */
    public const CANCELLED = 5;
}
