<?php

namespace App\Objects;

use App\Enums\Form\ProductTaxDistinction;
use stdClass;

class FormProductCalculations
{
    /**
     * Calculate total prices and taxes of form products
     *
     * @param \Illuminate\Database\Eloquent\Collection $products
     * @return object|null
     */
    public static function getPricesAndTaxes($products)
    {
        // Return null if no products
        if (empty($products->count())) {
            return null;
        }

        // Calculate data
        $totalPrice = self::calculateTotalPrice($products);
        $tax10Percent = self::calculateTenPercentTax($products);
        $tax8Percent = self::calculateEightPercentTax($products);

        // Data object
        $pricesAndTaxes = new stdClass();
        $pricesAndTaxes->tax_10_percent = $tax10Percent;
        $pricesAndTaxes->tax_8_percent = $tax8Percent;
        $pricesAndTaxes->total_price = $totalPrice;
        $pricesAndTaxes->total_tax = round($tax10Percent + $tax8Percent) ?? 0;
        $pricesAndTaxes->overall_total = round($totalPrice + $tax10Percent + $tax8Percent) ?? 0;

        return $pricesAndTaxes;
    }

    /**
     * Calculate total price
     *
     * @param \Illuminate\Database\Eloquent\Collection $products
     * @return int|float
     */
    public static function calculateTotalPrice($products)
    {
        $prices = [];

        foreach ($products as $product) {
            $prices[] = self::calculateTotalAmount($product);
        }

        return round(array_sum($prices)) ?? 0;
    }

    /**
     * Calculate total price with 10% tax
     *
     * @param \Illuminate\Database\Eloquent\Collection $products
     * @return int|float
     */
    public static function calculateTenPercentTax($products)
    {
        $taxes = [];

        foreach ($products as $product) {
            if ($product->tax_distinction === ProductTaxDistinction::PERCENT_10) {
                $amount = self::calculateTotalAmount($product);
                $taxes[] = $amount * 0.1; // 10% = 0.1
            }
        }

        return round(array_sum($taxes)) ?? 0;
    }

    /**
     * Calculate total price with 8% tax
     *
     * @param \Illuminate\Database\Eloquent\Collection $products
     * @return int|float
     */
    public static function calculateEightPercentTax($products)
    {
        $taxes = [];

        foreach ($products as $product) {
            if ($product->tax_distinction === ProductTaxDistinction::REDUCTION_8_PERCENT) {
                $amount = self::calculateTotalAmount($product);
                $taxes[] = $amount * 0.08; // 8% = 0.08
            }
        }

        return round(array_sum($taxes)) ?? 0;
    }

    /**
     * Calculate single product total amount.
     *
     * @param \App\Models\FormProduct $product
     * @return int|float
     */
    public static function calculateTotalAmount($product)
    {
        // Return 0 if no product
        if (empty($product)) {
            return 0;
        }

        // Get computable data
        $unitPrice = $product->unit_price ?? 0;
        $quantity = $product->quantity ?? 0;

        return (float)$unitPrice * (int)$quantity;
    }

    /**
     * Calculate single product tax amount.
     *
     * @param \App\Models\FormProduct $product
     * @return int|float
     */
    public static function calculateTaxAmount($product)
    {
        // Return 0 if no product
        if (empty($product)) {
            return 0;
        }

        // Get total amount
        $amount = self::calculateTotalAmount($product);

        switch ($product->tax_distinction) {
            case ProductTaxDistinction::PERCENT_10:
                return $amount * 0.1; // 10% = 0.1
            case ProductTaxDistinction::REDUCTION_8_PERCENT:
                return $amount * 0.08; // 8% = 0.08
            default:
                return 0;
        }
    }

    /**
     * Calculate single product total.
     *
     * @param \App\Models\FormProduct $product
     * @return int|float
     */
    public static function calculateProductTotal($product)
    {
        // Return 0 if no product
        if (empty($product)) {
            return 0;
        }

        // Get computable data
        $amount = self::calculateTotalAmount($product);
        $tax = self::calculateTaxAmount($product);

        return round($amount + $tax) ?? 0;
    }
}
