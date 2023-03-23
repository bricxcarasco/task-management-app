<?php

namespace App\Http\Requests\Rio;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Rules\ReferenceUrlNoSpaces;
use App\Rules\ReferenceUrlUnique;

class UpsertProductRequest extends FormRequest
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
            'content' => 'required|string|max:100',
            'additional' => 'required|string|max:50',
            'reference_url' => [
                'nullable',
                'url',
                new ReferenceUrlNoSpaces(),
                new ReferenceUrlUnique($this->id, 'rio'),
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
            'content' => __('Product Name'),
            'additional' => __('Product Description'),
            'product_image' => __('Image'),
            'reference_url' => __('Reference URL'),
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
