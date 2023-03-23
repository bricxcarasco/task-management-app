<?php

/**
 * Secret Question Constant Values
 */

namespace App\Enums\Rio;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Contracts\LocalizedEnum;

/**
 * App\Enums\Rio\SecretQuestionType
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class SecretQuestionType extends Enum implements LocalizedEnum
{
    /**
     * First secret question
     *
     * @var int
     */
    public const FIRST_QUESTION = 1;

    /**
     * Second secret question
     *
     * @var int
     */
    public const SECOND_QUESTION = 2;

    /**
     * Third secret question
     *
     * @var int
     */
    public const THIRD_QUESTION = 3;

    /**
     * Get secret question values by key-value array
     *
     * @return array
     */
    public static function getSecretQuestions()
    {
        $secretQuestionArr = [];
        foreach (self::getValues() as $secretQuestion) {
            $secretQuestionArr[$secretQuestion]['key'] = $secretQuestion;
            $secretQuestionArr[$secretQuestion]['value'] = get_secret_question_by_id($secretQuestion);
        }
        return $secretQuestionArr;
    }
}
