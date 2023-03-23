<?php

/**
 * Chat Statuses
 */

namespace App\Enums\Chat;

use BenSampo\Enum\Enum;

/**
 * App\Enums\Chat\ChatStatuses
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class ChatStatuses extends Enum
{
    /**
     * Status - Active
     *
     * @var string
     */
    public const ACTIVE = 'active';

    /**
     * Status - Archive
     *
     * @var string
     */
    public const ARCHIVE = 'archive';
}
