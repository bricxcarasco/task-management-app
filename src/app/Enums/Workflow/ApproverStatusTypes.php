<?php

/**
 * Workflow - ApproverStatusTypes
 */

namespace App\Enums\Workflow;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Contracts\LocalizedEnum;

/**
 * App\Enums\Workflow\ApproverStatusTypes
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class ApproverStatusTypes extends Enum implements LocalizedEnum
{
    /**
     * Pending Approver Status
     *
     * @var int
     */
    public const PENDING =   1;

    /**
     * Done Approver Status
     *
     * @var int
     */
    public const DONE =   2;

    /**
     * Completed Approver Status
     *
     * @var int
     */
    public const COMPLETED = 3;

    /**
     * Rejected Approver Status
     *
     * @var int
     */
    public const REJECTED = 4;

    /**
     * Cancelled Approver Status
     *
     * @var int
     */
    public const CANCELLED = 5;
}
