<?php

use App\Enums\Classified\SaleProductVisibility;
use App\Enums\Rio\GenderType;
use App\Enums\Rio\SecretQuestionType;
use App\Enums\PrefectureTypes;
use App\Enums\Neo\OrganizationAttributeType;
use App\Enums\Neo\AcceptConnectionType;
use App\Enums\Neo\AcceptBelongType;
use App\Enums\Neo\OverseasSupportType;
use App\Models\ClassifiedSaleCategory;

if (!function_exists('get_secret_question_by_id')) {
    /**
     * @param int $id
     */
    function get_secret_question_by_id($id): string
    {
        return SecretQuestionType::getDescription($id);
    }
}

if (!function_exists('gender_value')) {
    /**
     * @param int $id
     */
    function gender_value($id): string
    {
        return GenderType::getDescription($id);
    }
}

if (!function_exists('prefecture_value')) {
    /**
     * @param int $id
     */
    function prefecture_value($id): string
    {
        return PrefectureTypes::getDescription($id);
    }
}

if (!function_exists('organization_type_value')) {
    /**
     * @param int $id
     */
    function organization_type_value($id): string
    {
        return OrganizationAttributeType::getDescription($id);
    }
}

if (!function_exists('neo_accept_connection_value')) {
    /**
     * @param int $id
     */
    function neo_accept_connection_value($id): string
    {
        return AcceptConnectionType::getDescription($id);
    }
}

if (!function_exists('neo_accept_belong_value')) {
    /**
     * @param int $id
     */
    function neo_accept_belong_value($id): string
    {
        return AcceptBelongType::getDescription($id);
    }
}

if (!function_exists('overseas_value')) {
    /**
     * @param int $id
     */
    function overseas_value($id): string
    {
        return OverseasSupportType::getDescription($id);
    }
}

if (!function_exists('publishing_setting')) {
    /**
     * @param int $id
     */
    function publishing_setting($id): string
    {
        return SaleProductVisibility::getDescription($id);
    }
}

if (!function_exists('sale_category')) {
    /**
     * @param int $id
     */
    function sale_category($id): string
    {
        /** @var ClassifiedSaleCategory */
        $productCategory = ClassifiedSaleCategory::whereId($id)
            ->first();

        return $productCategory->sale_category_name;
    }
}
