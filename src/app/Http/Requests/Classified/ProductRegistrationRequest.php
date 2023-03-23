<?php

namespace App\Http\Requests\Classified;

use App\Enums\Classified\SaleProductVisibility;
use App\Rules\ValidFilepondCode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRegistrationRequest extends FormRequest
{
    /**
     * The route to redirect to if validation fails.
     *
     * @var string
     */
    protected $redirectRoute = 'classifieds.sales.create';

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
        if ($this->has('price') && $this->has('set_quote')) {
            $this->merge(['price'=> null]);
        }

        if ($this->has('upload_file')) {
            $this->merge([
                'upload_file' => array_filter($this->get('upload_file')),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'upload_file' => [
                'nullable',
                'array',
            ],
            'upload_file.*' => [
                'required',
                'string',
                new ValidFilepondCode(),
            ],
            'sale_category' => [
                'required',
                'exists:classified_sale_categories,id'
            ],
            'title' => [
                'required',
                'string',
                'max:100',
            ],
            'detail' => [
                'required',
                'string',
                'max:1000',
            ],
            'price' => [
                'nullable',
                'integer',
                'required_without:set_quote',
                'between:1,99999999'
            ],
            'set_quote' => [
                'nullable',
            ],
            'is_public' => [
                'required',
                Rule::in(SaleProductVisibility::getValues()),
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
            'sale_category' => __('Product category'),
            'title' => __('Product name'),
            'price' => __('Price tax shipping'),
            'set_quote' => __('Set price to individual quote'),
            'detail' => __('Product description'),
            'is_public' => __('Publishing Settings'),
        ];
    }
}
