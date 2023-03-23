<?php

namespace App\Enums\Neo;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Contracts\LocalizedEnum;

/**
 * App\Enums\Neo\OrganizationAttributeType
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class OrganizationAttributeType extends Enum implements LocalizedEnum
{
    /**
     * Corporate
     *
     * @var int
     */
    public const CORPORATE = 1;

    /**
     * NPO
     *
     * @var int
     */
    public const NPO = 2;

    /**
     * School
     *
     * @var int
     */
    public const SCHOOL = 3;

    /**
     * Alumni
     *
     * @var int
     */
    public const ALUMNI = 4;
}
