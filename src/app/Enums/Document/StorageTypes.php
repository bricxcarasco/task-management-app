<?php

namespace App\Enums\Document;

use BenSampo\Enum\Enum;

/**
 * App\Enums\Document\StorageTypes
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class StorageTypes extends Enum
{
    /**
     * Storage type - Hero
     *
     * @var int
     */
    public const HERO = 1;

    /**
     * Storage type - Google Drive
     *
     * @var int
     */
    public const GOOGLE_DRIVE = 2;

    /**
     * Storage type - Dropbox
     *
     * @var int
     */
    public const DROPBOX = 3;
}
