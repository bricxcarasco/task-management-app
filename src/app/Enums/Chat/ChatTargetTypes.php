<?php

/**
 * Chat Target Types
 */

namespace App\Enums\Chat;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Contracts\LocalizedEnum;

/**
 * App\Enums\Chat\ChatTargetTypes
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class ChatTargetTypes extends Enum implements LocalizedEnum
{
    /**
     * Type - All
     *
     * @var int
     */
    public const ALL = 'All';

    /**
     * Type - RIO
     *
     * @var int
     */
    public const RIO = 'RIO';

    /**
     * Type - NEO
     *
     * @var int
     */
    public const NEO = 'NEO';
}
