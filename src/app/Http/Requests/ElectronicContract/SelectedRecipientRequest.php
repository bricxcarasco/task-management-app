<?php

namespace App\Http\Requests\ElectronicContract;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\ServiceSelectionTypes;

class SelectedRecipientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'nullable',
            'name' => 'nullable',
            'service' => ['nullable', Rule::in(ServiceSelectionTypes::getValues())],
            'profile_photo' => 'nullable',
        ];
    }
}
