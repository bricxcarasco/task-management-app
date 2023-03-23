<?php

/**
 * Chat Types
 */

namespace App\Enums\Chat;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Contracts\LocalizedEnum;

/**
 * App\Enums\Chat\ChatTypes
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class ChatTypes extends Enum implements LocalizedEnum
{
    /**
     * Type - Connected Chat
     *
     * @var int
     */
    public const CONNECTED = 1;

    /**
     * Type - Connected Group Chat
     *
     * @var int
     */
    public const CONNECTED_GROUP = 2;

    /**
     * Type - Neo Group Chat
     *
     * @var int
     */
    public const NEO_GROUP = 3;

    /**
     * Type - Neo Message Chat
     *
     * @var int
     */
    public const NEO_MESSAGE = 4;
}
