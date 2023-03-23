<?php

/**
 * PaidPlan - Service types
 */

namespace App\Enums\PaidPlan;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Contracts\LocalizedEnum;

/**
 * App\Enums\PaidPlan\ServiceType
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class ServiceType extends Enum implements LocalizedEnum
{
    /**
     * Registered People
     *
     * @var int
     */
    public const REGISTERED_PEOPLE = 1;

    /**
     * Online Shop
     *
     * @var int
     */
    public const ONLINE_SHOP = 2;

    /**
     * Webinar
     *
     * @var int
     */
    public const WEBINAR = 3;

    /**
     * Electronic Contract
     *
     * @var int
     */
    public const ELECTRONIC_CONTRACT = 4;

    /**
     * Knowledge
     *
     * @var int
     */
    public const KNOWLEDGE = 5;

    /**
     * Document Management
     *
     * @var int
     */
    public const DOCUMENT_MANAGEMENT = 6;

    /**
     * Advertisement
     *
     * @var int
     */
    public const ADVERTISEMENT = 7;

    /**
     * Quotation Issuance
     *
     * @var int
     */
    public const QUOTATION = 8;

    /**
     * Purchase Order Issuance
     *
     * @var int
     */
    public const PURCHASE_ORDER = 9;

    /**
     * Delivery Slip Issuance
     *
     * @var int
     */
    public const DELIVERY_SLIP = 10;

    /**
     * Invoice Issuance
     *
     * @var int
     */
    public const INVOICE = 11;

    /**
     * Receipt Issuance
     *
     * @var int
     */
    public const RECEIPT = 12;

    /**
     * Workflow
     *
     * @var int
     */
    public const WORKFLOW = 13;

    /**
     * Network Map
     *
     * @var int
     */
    public const NETWORK_MAP = 14;
}
