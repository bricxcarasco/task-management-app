<?php

namespace App\Enums\Knowledge;

use BenSampo\Enum\Enum;

/**
 * App\Enums\Knowledge\Types
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class Types extends Enum
{
    /**
     * Document type - folder
     *
     * @var int
     */
    public const FOLDER = 1;

    /**
     * Document type - article
     *
     * @var int
     */
    public const ARTICLE = 2;
}
