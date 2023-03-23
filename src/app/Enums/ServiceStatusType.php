<?php

/**
 * Service Status Types Constants
 */

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * App\Enums\ServiceStatusType
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class ServiceStatusType extends Enum
{
    /**
     * Inactive service feature
     *
     * @var int
     */
    public const INACTIVE = 0;

    /**
     * Active service feature
     *
     * @var int
     */
    public const ACTIVE = 1;
}
