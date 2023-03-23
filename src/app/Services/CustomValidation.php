<?php

namespace App\Services;

class CustomValidation extends \Illuminate\Validation\Validator
{
    /**
     * validate contains alphanumeric.
     *
     * @param string $attribute
     * @param string $value
     * @param array  $parameters
     *
     * @return bool|int
     */
    public function validateContainAlphaNumeric($attribute, $value, $parameters)
    {
        return preg_match('/\\A(?=.*?[a-zA-Z])(?=.*?\\d).+\\z/', $value);
    }

    /**
     * validate alphanumeric + symbol.
     *
     * @param string $attribute
     * @param string $value
     * @param array  $parameters
     *
     * @return bool|int
     */
    public function validateAlphaNumericSymbol($attribute, $value, $parameters)
    {
        return preg_match('/\\A[a-zA-Z0-9\\!-\\/\\:-\\@\\¥\\[-\\`\\{-\\~]+\\z/', $value);
    }

    /**
     * validate katakana.
     *
     * @param string $attribute
     * @param string $value
     * @param array  $parameters
     *
     * @return bool|int
     */
    public function validateKatakana($attribute, $value, $parameters)
    {
        return preg_match('/\\A[ァ-ヶー 　・]+\\z/u', $value);
    }

    /**
     * validate double space.
     *
     * @param string $attribute
     * @param string $value
     * @param array  $parameters
     *
     * @return bool
     */
    public function validateDoubleSpace($attribute, $value, $parameters)
    {
        $strip = trim(mb_convert_kana($value, 's', 'UTF-8'));

        if (empty($strip)) {
            return false;
        }

        return true;
    }
}
