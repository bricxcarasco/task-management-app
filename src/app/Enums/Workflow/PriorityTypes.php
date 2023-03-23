<?php

/**
 * Workflow - PriorityTypes
 */

namespace App\Enums\Workflow;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Contracts\LocalizedEnum;

/**
 * App\Enums\Workflow\PriorityTypes
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class PriorityTypes extends Enum implements LocalizedEnum
{
    /**
     * High PriorityTypes
     *
     * @var string
     */
    public const NONE = 'none';

    /**
     * High PriorityTypes
     *
     * @var string
     */
    public const HIGH =  "high";

    /**
     * Mid PriorityTypes
     *
     * @var string
     */
    public const MID =   "mid";

    /**
     * Low PriorityTypes
     *
     * @var string
     */
    public const LOW = "low";
}
