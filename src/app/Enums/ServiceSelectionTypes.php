<?php

/**
 * Service Selection - Constant Values
 */

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * App\Enums\NeoBelongStatuses
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class ServiceSelectionTypes extends Enum
{
    /**
     * Service Selection NEO
     *
     * @var int
     */
    public const NEO = 'NEO';

    /**
     * Service Selection RIO
     *
     * @var int
     */
    public const RIO = 'RIO';
}
