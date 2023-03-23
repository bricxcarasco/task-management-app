<?php

namespace App\Enums\Chat;

use BenSampo\Enum\Enum;

/**
 * App\Enums\Connection\GroupStatuses
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class ConnectedChatType extends Enum
{
    /**
     * In process of application / requesting
     * 申請中
     *
     * @var int
     */
    public const RIO_TO_RIO = 1;

    /**
     * Connected state
     * つながり状態
     *
     * @var int
     */
    public const RIO_TO_NEO = 2;

    /**
     * Connected state
     * つながり状態
     *
     * @var int
     */
    public const NEO_TO_NEO = 3;

    /**
    * Connected state
    * つながり状態
    *
    * @var int
    */
    public const NEO_TO_RIO = 4;
}
