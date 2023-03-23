<?php

namespace App\Enums\Neo;

use BenSampo\Enum\Enum;

/**
 * App\Enums\Neo\NeoBelongStatusType
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class NeoBelongStatusType extends Enum
{
    /**
    * Applying status
    *
    * @var int
    */
    public const APPLYING = 0;

    /**
    * AFFILIATE status
    *
    * @var int
    */
    public const AFFILIATE = 1;

    /**
    * INVITING status
    *
    * @var int
    */
    public const INVITING = 2;
}
