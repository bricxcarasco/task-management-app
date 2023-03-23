<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ReferenceUrlNoSpaces implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return !str_contains($value, " ");
    }

    /**
     * Get the validation error message.
     *
     * @return array|string|null
     */
    public function message()
    {
        return __('Space characters are not allowed in the reference URL.');
    }
}
