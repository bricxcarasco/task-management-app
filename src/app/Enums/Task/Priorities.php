<?php

/**
 * Task - Priorities
 */

namespace App\Enums\Task;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

/**
 * App\Enums\Task\Priority
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class Priorities extends Enum implements LocalizedEnum
{
    /**
     * Low priority
     *
     * @var int
     */
    public const LOW = 'low';

    /**
     * Mid priority
     *
     * @var int
     */
    public const MID = 'mid';

    /**
     * High priority
     *
     * @var int
     */
    public const HIGH = 'high';
}
