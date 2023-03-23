<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Enums\ServiceSelectionTypes;
use Session;

class ProfileImageServiceSelection implements Rule
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
        /** @var \App\Models\User */
        $user = auth()->user();

        $isAuthorized = false;

        // Service selection session state values
        $service = json_decode(Session::get('ServiceSelected'));

        if ($service->type === ServiceSelectionTypes::RIO) {
            if ($user->rio_id === $value) {
                $isAuthorized = true;
            }
        }

        if ($service->type === ServiceSelectionTypes::NEO) {
            $neosArray = $user->rio->neos->pluck('id')->toArray();
            if ($service->data->id === $value && in_array($value, $neosArray)) {
                $isAuthorized = true;
            }
        }

        return $isAuthorized;
    }

    /**
     * Get the validation error message.
     *
     * @return array|string|null
     */
    public function message()
    {
        return __('Unauthorized');
    }
}
