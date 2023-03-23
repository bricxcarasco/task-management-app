<?php

namespace App\Enums\Connection;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Contracts\LocalizedEnum;

/**
 * App\Enums\Connection\ListFilters
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class ListFilters extends Enum implements LocalizedEnum
{
    /**
     * Display both Rio and Neo
     *
     * @var int
     */
    public const SHOW_ALL = 0;

    /**
     * Display Rio only
     *
     * @var int
     */
    public const ONLY_RIO = 1;

    /**
     * Display Neo only
     *
     * @var int
     */
    public const ONLY_NEO = 2;
}
