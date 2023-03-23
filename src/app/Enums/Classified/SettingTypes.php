<?php

/**
 * Settings
 */

namespace App\Enums\Classified;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

/**
 * App\Enums\MessageSender
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class SettingTypes extends Enum implements LocalizedEnum
{
    /**
     * Savings
     *
     * @var int
     */
    public const SAVINGS = 1;

    /**
     * Current
     *
     * @var int
     */
    public const CURRENT = 2;
}
