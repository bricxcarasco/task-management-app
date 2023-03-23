<?php

namespace App\Http\Requests\Rio;

use App\Enums\PrefectureTypes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdatePresentAddressRequest extends FormRequest
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
            'present_address_prefecture' => [
                'required',
                Rule::in(PrefectureTypes::getValues())
            ],
            'present_address_city' => 'nullable|max:255',
            'present_address' => 'nullable|max:255',
            'present_address_building' => 'nullable|max:255',
            'present_address_nationality' => [
                'required_if:present_address_prefecture,0',
                'max:255',
            ],
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        $response = response()->respondInvalidParameters($validator->errors());

        throw new HttpResponseException($response);
    }
}
