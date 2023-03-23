<?php

namespace App\Enums\Document;

use BenSampo\Enum\Enum;

/**
 * App\Enums\Document\StorageTypes
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class DocumentListTypes extends Enum
{
    /**
     * Document list type - upload
     *
     * @var int
     */
    public const UPLOAD = 1;

    /**
     * Document list type - shared
     *
     * @var int
     */
    public const SHARED = 2;

    /**
     * Document list type personal
     *
     * @var int
     */
    public const PERSONAL = 3;
}
