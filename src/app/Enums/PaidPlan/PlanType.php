<?php

/**
 * PaidPlan - Plan types
 */

namespace App\Enums\PaidPlan;

use BenSampo\Enum\Enum;

/**
 * App\Enums\PaidPlan\PlanType
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class PlanType extends Enum
{
    /**
     * RIO Free Plan
     *
     * @var int
     */
    public const RIO_FREE_PLAN = 1;

    /**
     * RIO Light Plan
     *
     * @var int
     */
    public const RIO_LIGHT_PLAN = 2;

    /**
     * RIO Standard Plan
     *
     * @var int
     */
    public const RIO_STANDARD_PLAN = 3;

    /**
     * RIO Premium Plan
     *
     * @var int
     */
    public const RIO_PREMIUM_PLAN = 4;

    /**
     * NEO Free Plan
     *
     * @var int
     */
    public const NEO_FREE_PLAN = 5;

    /**
     * NEO Light Plan
     *
     * @var int
     */
    public const NEO_LIGHT_PLAN = 6;

    /**
     * NEO Standard Plan
     *
     * @var int
     */
    public const NEO_STANDARD_PLAN = 7;

    /**
     * NEO Premium Plan
     *
     * @var int
     */
    public const NEO_PREMIUM_PLAN = 8;
}
