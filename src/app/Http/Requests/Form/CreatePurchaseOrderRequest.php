<?php

namespace App\Http\Requests\Form;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Enums\Form\ProductTaxDistinction;
use App\Enums\Form\Types;

class CreatePurchaseOrderRequest extends FormRequest
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
            'form_no' => 'nullable|string|max:100',
            'zipcode' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'title' => 'required|string|max:255',
            'issue_date' => 'required|date',
            'delivery_date' => 'nullable|date',
            'delivery_address' => 'nullable|string',
            'payment_terms' => 'nullable|string',
            'remarks' => 'nullable|string',
            'logo' => [
                'nullable',
            ],
            'issuer_name' => 'required|string|max:255',
            'issuer_department_name' => 'nullable|string|max:255',
            'issuer_address' => 'nullable|string|max:255',
            'issuer_tel' => 'nullable|string|regex:/(^[\d\-\+]+)$/u',
            'issuer_fax' => 'nullable|string|regex:/(^[\d\-\+]+)$/u',
            'issuer_business_number' => 'nullable|string|max:255',
            'supplier_name' => 'required|string|max:255',
            'supplier' => 'required',
            'supplier.id' => 'required',
            'supplier.service' => 'required',
            'supplier.name' => 'required',
            'is_supplier_connected' => 'nullable',
            'products' => 'required|array',
            'products.*.name' => 'required|string|max:255',
            'products.*.quantity' => 'nullable|integer',
            'products.*.unit' => 'nullable|string|max:255',
            'products.*.unit_price' => 'nullable|integer',
            'total_price' => 'nullable|integer',
            'products.*.tax_distinction' => [
                'nullable',
                'integer',
                Rule::in(ProductTaxDistinction::getValues()),
            ]
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
            'mode',
            'logo',
            'supplier',
            'products',
            'invoice_no',
            'total_price',
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
            'form_no' => __('Purchase Order No'),
            'zipcode' => __('Postal code'),
            'title' => __('Subject'),
            'issue_date' => __('Date of issue'),
            'payment_terms' => __('Payment terms'),
            'logo' => __('Logo'),
            'issuer_name' => __('Store and trade name'),
            'issuer_address' => __('Address'),
            'issuer_tel' => __('Tel'),
            'issuer_fax' => __('Fax'),
            'issuer_business_number' => __('Business number'),
            'supplier' => __('Supplier'),
            'supplier_name' => __('Supplier'),
            'products' => __('Registered Products'),
            'products.*.name' => __('Product name'),
            'products.*.quantity' => __('Quantity'),
            'products.*.unit' => __('Unit'),
            'products.*.unit_price' => __('Unit price'),
            'products.*.tax_distinction' => __('Tax classification'),
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
