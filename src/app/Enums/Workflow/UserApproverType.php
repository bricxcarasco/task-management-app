<?php

/**
 * Workflow - UserApproverTypes
 */

namespace App\Enums\Workflow;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

/**
 * App\Enums\Workflow\StatusTypes
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class UserApproverType extends Enum implements LocalizedEnum
{
    /**
     * High PriorityTypes
     *
     * @var string
     */
    public const CURRENT_APPROVER =  "current_approver";

    /**
     * Mid PriorityTypes
     *
     * @var string
     */
    public const OWNER =   "owner";

    /**
     * Low PriorityTypes
     *
     * @var string
     */
    public const APPROVER = "approver";
}
