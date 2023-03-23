<?php

namespace App\Enums\Rio;

use BenSampo\Enum\Enum;

/**
 * App\Enums\Rio\ConnectionStatusType
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class ConnectionStatusType extends Enum
{
    /**
     * Rio is allowed to connect to a specific Rio
     *
     * @var int
     */
    public const ALLOWED = 1;

    /**
     * Rio is not allowed to connect to a specific Rio
     *
     * @var int
     */
    public const NOT_ALLOWED = 2;

    /**
     * Rio connection is private
     *
     * @var int
     */
    public const HIDDEN = 3;

    /**
     * Rio can cancel the pending connection
     *
     * @var int
     */
    public const CANCELLATION = 4;

    /**
     * Rio can disconnect to a specific Rio (already connected)
     *
     * @var int
     */
    public const DISCONNECTION = 5;
}
