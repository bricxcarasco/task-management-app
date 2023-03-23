<?php

namespace App\Http\Requests\Form;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class QuotationSearchInputRequest extends FormRequest
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
            'isAmount' => $this->amount_min && $this->amount_max ? true : false,
            'isExpirationDate' => $this->expiration_start_date && $this->expiration_end_date ? true : false,
            'isIssuetDate' => $this->issue_start_date && $this->issue_end_date ? true : false,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules =  [
            'free_word' => [
                'nullable',
                'string',
            ],
            'issue_start_date' => [
                'nullable',
                'date_format:Y-m-d',
            ],
            'issue_end_date' => [
                'nullable',
                'date_format:Y-m-d',
            ],
            'expiration_start_date' => [
                'nullable',
                'date_format:Y-m-d',
            ],
            'expiration_end_date' => [
                'nullable',
                'date_format:Y-m-d',
            ],
            'amount_min' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'amount_max' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'is_search' => [
                'nullable',
                'boolean',
            ],
        ];

        if ($this->isAmount && $this->amount_min >= 0) {
            $rules['amount_max'] = 'gte:amount_min';
        }

        if ($this->isExpirationDate) {
            $rules['expiration_end_date'] = 'after_or_equal:expiration_start_date';
        }

        if ($this->isIssuetDate) {
            $rules['issue_end_date'] = 'after_or_equal:issue_start_date';
        }

        return $rules;
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
