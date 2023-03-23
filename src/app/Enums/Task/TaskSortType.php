<?php

/**
 * Task - Priorities
 */

namespace App\Enums\Task;

use BenSampo\Enum\Enum;

/**
 * App\Enums\Task\Priority
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class TaskSortType extends Enum
{
    /**
     * Registration newst
     *
     * @var int
     */
    public const REGISTRATION_NEWEST = 0;

    /**
     * Newest due date
     *
     * @var int
     */
    public const NEWEST_DUE_DATE = 1;

    /**
     * Oldest due date
     *
     * @var int
     */
    public const OLDEST_DUE_DATE = 2;

    /**
     * Oldest due date
     *
     * @var int
     */
    public const COMPLETED_AT = 3;
}
