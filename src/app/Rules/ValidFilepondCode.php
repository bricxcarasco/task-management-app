<?php

namespace App\Rules;

use App\Objects\FilepondFile;
use Illuminate\Contracts\Validation\Rule;

class ValidFilepondCode implements Rule
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
        return FilepondFile::isValidCode($value);
    }

    /**
     * Get the validation error message.
     *
     * @return array|string|null
     */
    public function message()
    {
        return __('validation.regex');
    }
}
