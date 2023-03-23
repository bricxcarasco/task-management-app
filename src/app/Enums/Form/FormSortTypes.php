<?php

/**
 * Form - Form sort types
 */

namespace App\Enums\Form;

use BenSampo\Enum\Enum;

/**
 * App\Enums\Form\FormSortTypes
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class FormSortTypes extends Enum
{
    /**
     * Newest issue date
     *
     * @var int
     */
    public const NEWEST_ISSUE_DATE = 0;

    /**
     * Oldest issue date
     *
     * @var int
     */
    public const OLDEST_ISSUE_DATE = 1;
}
