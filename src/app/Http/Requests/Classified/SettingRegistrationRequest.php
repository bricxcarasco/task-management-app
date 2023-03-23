<?php

namespace App\Http\Requests\Classified;

use App\Enums\Classified\SettingTypes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class SettingRegistrationRequest extends FormRequest
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
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'bank' => preg_replace('/\s+/', ' ', $this->bank),
            'branch' => preg_replace('/\s+/', ' ', $this->branch),
            'account_type' => $this->account_type ?? null,
            'account_number' => $this->account_number ?? null,
            'account_name' => preg_replace('/\s+/', ' ', $this->account_name),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'bank' => [
                'required',
                'regex:/(^[\pL0-9 ]+)$/u',
                'max:100',
            ],
            'branch' => [
                'required',
                'regex:/(^[\pL0-9 ]+)$/u',
                'max:100',
            ],
            'account_type' => [
                'required',
                Rule::in(SettingTypes::getValues()),
            ],
            'account_number' => [
                'required',
                'numeric',
                'digits_between:7,7',
            ],
            'account_name' => [
                'required',
                'regex:/(^[\pL0-9 ]+)$/u',
                'max:100',
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
            'bank' => __('Bank name'),
            'branch' => __('Branch'),
            'account_type' => __('Account Type'),
            'account_number' => __('Account Number'),
            'account_name' => __('Account Name'),
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
