<?php

/**
 * Plan Service Types Constants
 */

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * App\Enums\PlanServiceType
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class PlanServiceType extends Enum
{
    /**
     * Plan Service - Plan
     *
     * @var int
     */
    public const PLAN = 1;

    /**
     * Plan Service - Option
     *
     * @var int
     */
    public const OPTION = 2;
}
