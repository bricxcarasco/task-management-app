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
final class TaskStatusType extends Enum
{
    /**
     * INCOMPLETE status
     *
     * @var int
     */
    public const INCOMPLETE = 0;

    /**
     * COMPLETION status
     *
     * @var int
     */
    public const COMPLETION = 1;
}
