<?php

namespace App\Http\Requests\Form;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class BasicSettingRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'department_name' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'tel' => 'nullable|string|regex:/(^[\d+]+)$/u',
            'fax' => 'nullable|string|regex:/(^[\d-]+)$/u',
            'business_number' => 'nullable|string|max:255',
            'payment_terms_one' => 'nullable|string|max:255',
            'payment_terms_two' => 'nullable|string|max:255',
            'payment_terms_three' => 'nullable|string|max:255',
            'image' => 'nullable',
            'delete_existing' => 'nullable'
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
            'name' => __('Business Name'),
            'department_name' => __('Department Name'),
            'address' => __('Business Address'),
            'business_number' => __('Business Number'),
            'payment_terms_one' => __('Payment terms'),
            'payment_terms_two' => __('Payment terms'),
            'payment_terms_three' => __('Payment terms'),
            'image' => __('Issuer Logo'),
            'tel' => 'TEL',
            'fax' => 'FAX',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'tel.regex' => __('messages.validation_error_invalid_tel'),
            'fax.regex' => __('messages.validation_error_invalid_fax'),
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
