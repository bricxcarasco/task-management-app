<?php

/**
 * Business Type
 */

namespace App\Enums\Classified;

use BenSampo\Enum\Enum;

/**
 * App\Enums\Classified\BusinessType
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class BusinessType extends Enum
{
    /**
     * Business type - Individual
     *
     * @var string
     */
    public const INDIVIDUAL = 'individual';

    /**
     * Business type - Company
     *
     * @var string
     */
    public const COMPANY = 'company';
}
