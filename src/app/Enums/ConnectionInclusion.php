<?php

/**
 * Include/exclude connected RIOs/NEOs Constants
 */

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * App\Enums\ConnectionInclusion
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class ConnectionInclusion extends Enum
{
    /**
     * Include connected RIOS/NEOS
     *
     * @var int
     */
    public const INCLUDED = 0;

    /**
     * Exclude connected RIOS/NEOS
     *
     * @var int
     */
    public const EXCLUDED = 1;
}
