<?php

namespace App\Http\Requests\Form;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Enums\Form\Types;

class CreateReceiptRequest extends FormRequest
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
            'mode' => 'required',
            'type' => [
                'required',
                'integer',
                Rule::in(Types::getValues()),
            ],
            'title' => 'required|string|max:255',
            'issue_date' => 'required|date',
            'receipt_date' => 'required|date',
            'receipt_amount' => 'required|integer',
            'refer_receipt_no' => 'nullable|string|max:255',
            'issuer_name' => 'required|string|max:255',
            'issuer_address' => 'nullable|string|max:255',
            'issuer_tel' => 'nullable|string|regex:/(^[\d\-\+]+)$/u',
            'issuer_business_number' => 'nullable|string|max:255',
            'supplier_name' => 'required|string|max:255',
            'is_supplier_connected' => 'nullable',
            'supplier' => 'required',
            'supplier.id' => 'required',
            'supplier.service' => 'required',
            'supplier.name' => 'required',
        ];
    }

    /**
     * forms register attributes
     *
     * @return array request
     */
    public function formAttributes()
    {
        return $this->except([
            'receipt_amount',
            'supplier',
            'supplier_name'
        ]);
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'title' => __('Receipt Title'),
            'supplier' => __('Supplier'),
            'issue_date' => __('Date of issue'),
            'receipt_date' => __('Receipt date 2'),
            'receipt_amount' => __('Receipt Amount'),
            'refer_receipt_no' => __('Refer Receipt Number'),
            'issuer_name' => __('Store and trade name'),
            'issuer_address' => __('Address'),
            'issuer_tel' => __('Tel'),
            'issuer_business_number' => __('Business number'),
            'supplier_name' => __('Supplier'),
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
