<?php

namespace App\Http\Requests\Rio;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdatePasswordRequest extends FormRequest
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
            'current_password' => [
                'required',
                'string',
                'between:8,20',
                'contain_alpha_numeric',
                'current_password',
            ],
            'new_password' => [
                'required',
                'string',
                'between:8,20',
                'contain_alpha_numeric',
                'different:current_password',
            ],
            'new_password_confirmation' => [
                'required',
                'string',
                'between:8,20',
                'contain_alpha_numeric',
                'same:new_password',
            ]
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
            'current_password' => __('Current Password'),
            'new_password' => __('New Password'),
            'new_password_confirmation' => __('New Password Confirm'),
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
