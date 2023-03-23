<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidEmailFormat implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return (preg_match('/(\+.*)(?=\@)/', $value) == 0) ? true : false;
    }

    /**
     * Get the validation error message.
     *
     * @return array|string|null
     */
    public function message()
    {
        return __('validation.email_alias');
    }
}
