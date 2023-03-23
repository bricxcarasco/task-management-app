<?php

namespace App\Enums\Document;

use BenSampo\Enum\Enum;

/**
 * App\Enums\Document\StorageTypes
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class DocumentTypes extends Enum
{
    /**
     * Document type - folder
     *
     * @var int
     */
    public const FOLDER = 1;

    /**
     * Document type - file
     *
     * @var int
     */
    public const FILE = 2;

    /**
     * Document type - attaachment
     *
     * @var int
     */
    public const ATTACHMENT = 3;
}
