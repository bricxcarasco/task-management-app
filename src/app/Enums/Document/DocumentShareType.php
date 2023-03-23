<?php

namespace App\Enums\Document;

use BenSampo\Enum\Enum;

/**
 * App\Enums\Document\StorageTypes
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class DocumentShareType extends Enum
{
    /**
     * Document share type to RIO
     *
     * @var int
     */
    public const RIO = 1;

    /**
     * Document share type to NEO
     *
     * @var int
     */
    public const NEO = 2;

    /**
     * Document share type to NEO group
     *
     * @var int
     */
    public const NEO_GROUP = 3;
}
