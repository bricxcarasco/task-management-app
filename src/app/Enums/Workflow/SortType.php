<?php

/**
 * Workflow - SortType
 */

namespace App\Enums\Workflow;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Contracts\LocalizedEnum;

/**
 * App\Enums\Workflow\SortType
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class SortType extends Enum implements LocalizedEnum
{
    /**
     * Newest due date
     *
     * @var int
     */
    public const NEWEST_APPLICATION_DATE = 1;

    /**
     * Oldest due date
     *
     * @var int
     */
    public const OLDEST_APPLICATION_DATE = 2;
}
