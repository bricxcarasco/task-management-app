<?php

namespace App\Http\Requests\Classified;

use App\Enums\Classified\PaymentMethods;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\Exceptions\HttpResponseException;

class PaymentIssuanceRequest extends FormRequest
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
            'classified_sale_id' => [
                'required',
                'integer',
                'exists:classified_sales,id',
            ],
            'classified_contact_id' => [
                'required',
                'integer',
                'exists:classified_contacts,id',
            ],
            'rio_id' => [
                'nullable',
                'integer',
                'exists:rios,id',
            ],
            'neo_id' => [
                'nullable',
                'integer',
                'exists:neos,id',
            ],
            'price' => [
                'required',
                'integer',
                'between:500,300000',
            ],
            'payment_method' => [
                'required',
                Rule::in(PaymentMethods::getValues())
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
            'price' => __('Transaction Amount'),
            'payment_method' => __('Payment Method'),
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
