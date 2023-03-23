<?php

namespace App\Http\Requests\Rio;

use App\Enums\PrefectureTypes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateHomeAddressRequest extends FormRequest
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
            'home_address_prefecture' => [
                'required',
                Rule::in(PrefectureTypes::getValues())
            ],
            'home_address_city' => 'nullable|string|max:255',
            'home_address_nationality' => [
                'required_if:home_address_prefecture,' . PrefectureTypes::OTHER,
                'max:255',
            ],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'home_address_prefecture' => __('Home Address Prefecture'),
            'home_address_nationality' => __('Country'),
            'home_address_city' => __('Home Address City'),
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
