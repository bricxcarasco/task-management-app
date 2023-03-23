<?php

/**
 * Stripe Requirement Errors
 */

namespace App\Enums\Classified;

use BenSampo\Enum\Enum;

/**
 * App\Enums\Classified\StripeRequirementErrors
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class StripeRequirementErrors extends Enum
{
    /**
     * Pending Verification
     *
     * @var string
     */
    public const PENDING_VERIFICATION = 'requirements.pending_verification';

    /**
     * Identity Document
     *
     * @var string
     */
    public const IDENTITY_DOCUMENT = 'verification.document';

    /**
     * Bank account or debit card
     *
     * @var string
     */
    public const BANK_ACCOUNT = 'external_account';

    /**
     * Director's date of birth
     *
     * @var string
     */
    public const DIRECTOR_DOB = 'directors.dob.day';

    /**
     * Director's First name Kana
     *
     * @var string
     */
    public const DIRECTOR_FIRST_KANA = 'directors.first_name_kana';

    /**
     * Director's Last name Kana
     *
     * @var string
     */
    public const DIRECTOR_LAST_KANA = 'directors.last_name_kana';

    /**
     * Director's First name Kanji
     *
     * @var string
     */
    public const DIRECTOR_FIRST_KANJI = 'directors.first_name_kanji';

    /**
     * Director's Last name Kanji
     *
     * @var string
     */
    public const DIRECTOR_LAST_KANJI = 'directors.last_name_kanji';

    /**
     * Japan RISA compliance
     *
     * @var string
     */
    public const JAPAN_RISA_COMPLIANCE = 'jp_risa_compliance_survey';

    /**
     * Terms of service acceptance
     *
     * @var string
     */
    public const TERMS_OF_ACCEPTANCE = 'tos_acceptance.date';

    /**
     * Owner information
     *
     * @var string
     */
    public const OWNER = 'person';

    /**
     * Owner Kana address
     *
     * @var string
     */
    public const OWNER_KANA_ADDRESS = 'address_kana.city';

    /**
     * Owner Kanji address
     *
     * @var string
     */
    public const OWNER_KANJI_ADDRESS = 'address_kanji.city';

    /**
     * Date of birth
     *
     * @var string
     */
    public const DOB = 'dob.day';

    /**
     * Email
     *
     * @var string
     */
    public const EMAIL = 'email';

    /**
     * Phone
     *
     * @var string
     */
    public const PHONE = 'phone';

    /**
     * First name Kana
     *
     * @var string
     */
    public const FIRST_NAME_KANA = 'first_name_kana';

    /**
     * First name Kanji
     *
     * @var string
     */
    public const FIRST_NAME_KANJI = 'first_name_kanji';

    /**
     * Last name Kana
     *
     * @var string
     */
    public const LAST_NAME_KANA = 'last_name_kana';

    /**
     * Last name Kanji
     *
     * @var string
     */
    public const LAST_NAME_KANJI = 'last_name_kanji';

    /**
     * Industry
     *
     * @var string
     */
    public const INDUSTRY = 'business_profile.mcc';

    /**
     * Product description
     *
     * @var string
     */
    public const DESCRIPTION = 'business_profile.product_description';

    /**
     * Business website
     *
     * @var string
     */
    public const WEBSITE = 'business_profile.url';

    /**
     * Business support phone
     *
     * @var string
     */
    public const BUSINESS_PHONE = 'business_profile.support_phone';

    /**
     * Company directors
     *
     * @var string
     */
    public const COMPANY_DIRECTORS = 'company.directors_provided';

    /**
     * Business name
     *
     * @var string
     */
    public const COMPANY_NAME = 'company.name';

    /**
     * Kana business name
     *
     * @var string
     */
    public const COMPANY_KANA_NAME = 'company.name_kana';

    /**
     * Kanji business name
     *
     * @var string
     */
    public const COMPANY_KANJI_NAME = 'company.name_kanji';

    /**
     * Business phone number
     *
     * @var string
     */
    public const COMPANY_PHONE = 'company.phone';

    /**
     * Business Tax ID
     *
     * @var string
     */
    public const COMPANY_TAX_ID = 'company.tax_id';
}
