<?php

namespace App\Http\Requests\Classified;

use App\Enums\Classified\SaleProductVisibility;
use App\Objects\FilepondFile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProductEditRequest extends FormRequest
{
    /**
     * The route to redirect to if validation fails.
     *
     * @var string
     */
    protected $redirectRoute = 'classifieds.sales.edit';

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
            $fileInput = array_filter($this->get('upload_file'));

            $parsedFiles = [];
            foreach ((array) $fileInput as $key => $item) {
                if (FilepondFile::isValidCode($item)) {
                    $parsedFiles['upload_file'][$key] = $item;

                    continue;
                }

                $parsedFiles['local_file'][$key] = rawurldecode($item);
            }

            $this->merge([
                'upload_file' => $parsedFiles['upload_file'] ?? [],
                'local_file' => $parsedFiles['local_file'] ?? [],
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
                function ($attribute, $value, $fail) {
                    $message = __('validation.general', [
                        'attribute' => __('File')
                    ]);

                    if (FilepondFile::isValidCode($value)) {
                        return;
                    }

                    $fail($message);
                },
            ],
            'local_file' => [
                'nullable',
                'array',
            ],
            'local_file.*' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    $message = __('validation.general', [
                        'attribute' => __('File')
                    ]);

                    // Prepare for path checking
                    $decoded = rawurldecode($value);
                    $pathInfo = pathinfo($decoded);

                    // Check if valid filename
                    if (!empty($pathInfo['extension']) && !empty($pathInfo['filename'])) {
                        return;
                    }

                    $fail($message);
                },
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
        // Inject upload and local file
        $inputs = array_merge($this->all(), [
            'upload_file' => $this->get('upload_file', []),
            'local_file' => $this->get('local_file', []),
        ]);

        // Prepare response
        $response = redirect()
            ->route($this->redirectRoute, ['classifiedSale' => $this->classifiedSale->id])
            ->withErrors($validator)
            ->withInput($inputs);

        throw (new ValidationException($validator, $response))
            ->errorBag($this->errorBag);
    }
}
