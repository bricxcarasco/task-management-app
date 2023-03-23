<?php

namespace App\Http\Requests\Form;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Enums\Form\ProductTaxDistinction;

class InvoiceProductRequest extends FormRequest
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
            'name' => 'nullable|string|max:255',
            'quantity' => 'nullable|integer',
            'unit' => 'nullable|string|max:255',
            'unit_price' => 'nullable|integer',
            'tax_distinction' => [
                'nullable',
                'integer',
                Rule::in(ProductTaxDistinction::getValues()),
            ],
            'amount' => 'nullable',
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
            'name' => __('Product name'),
            'quantity' => __('Quantity'),
            'unit' => __('Unit'),
            'unit_price' => __('Unit price'),
            'tax_distinction' => __('Tax classification'),
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
