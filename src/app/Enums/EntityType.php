<?php

/**
 * Entity Type Constants
 */

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * App\Enums\EntityType
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class EntityType extends Enum
{
    /**
     * RIO entity
     *
     * @var int
     */
    public const RIO = 0;

    /**
     * NEO entity
     *
     * @var int
     */
    public const NEO = 1;
}
