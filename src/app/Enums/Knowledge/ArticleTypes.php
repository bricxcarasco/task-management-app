<?php

/**
 * Article Types
 */

namespace App\Enums\Knowledge;

use BenSampo\Enum\Enum;

/**
 * App\Enums\Knowledge\ArticleTypes
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class ArticleTypes extends Enum
{
    /**
     * Article Type - PUBLIC
     *
     * @var int
     */
    public const PUBLIC = 0;

    /**
     * Article Type - DRAFT
     *
     * @var int
     */
    public const DRAFT = 1;
}
