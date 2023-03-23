<?php

/**
 * Workflow - StatusTypes
 */

namespace App\Enums\Workflow;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Contracts\LocalizedEnum;

/**
 * App\Enums\Workflow\StatusTypes
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class StatusTypes extends Enum implements LocalizedEnum
{
    /**
     * Applying StatusTypes
     *
     * @var int
     */
    public const APPLYING =   1;

    /**
     * Remanded StatusTypes
     *
     * @var int
     */
    public const REMANDED =   3;

    /**
     * Approved StatusTypes
     *
     * @var int
     */
    public const APPROVED = 2;

    /**
     * Rejected StatusTypes
     *
     * @var int
     */
    public const REJECTED = 4;

    /**
     * Cancelled StatusTypes
     *
     * @var int
     */
    public const CANCELLED = 5;
}
