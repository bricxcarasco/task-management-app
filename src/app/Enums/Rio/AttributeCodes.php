<?php

/**
 * Rio Expert - Attribute Codes
 */

namespace App\Enums\Rio;

use BenSampo\Enum\Enum;

/**
 * App\Enums\AttributeCodes
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class AttributeCodes extends Enum
{
    /**
     * Educational Background Attribute
     *
     * @var string
     */
    public const EDUCATIONAL_BACKGROUND = 'educational_background';

    /**
     * Neo Affiliates Attribute
     *
     * @var string
     */
    public const NEO_AFFILIATES = 'neo_affiliates';

    /**
     * Qualifications Attribute
     *
     * @var string
     */
    public const QUALIFICATIONS = 'qualifications';

    /**
     * Skills Attribute
     *
     * @var string
     */
    public const SKILLS = 'skills';

    /**
     * Awards Attribute
     *
     * @var string
     */
    public const AWARDS = 'awards';

    /**
     * Experience Attribute
     *
     * @var string
     */
    public const EXPERIENCE = 'experience';

    /**
     * Profession Attribute
     *
     * @var string
     */
    public const PROFESSION = 'profession';

    /**
     * URL Attribute
     *
     * @var string
     */
    public const URL = 'url';

    /**
     * Industry Attribute
     *
     * @var string
     */
    public const INDUSTRY = 'business_category';

    /**
     * Business Hours Attribute
     *
     * @var string
     */
    public const BUSINESS_HOURS = 'business_hours';

    /**
     * Overseas Attribute
     *
     * @var string
     */
    public const OVERSEAS = 'overseas';

    /**
     * Product Service Information Attribute
     *
     * @var string
     */
    public const PRODUCT_SERVICE_INFORMATION = 'product_service_information';

    /**
     * Email Address Attribute
     *
     * @var string
     */
    public const EMAIL = 'email';
}
