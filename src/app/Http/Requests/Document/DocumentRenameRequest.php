<?php

namespace App\Http\Requests\Document;

use App\Services\TransformValidationErrorService;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class DocumentRenameRequest extends DocumentRequest
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
        parent::prepareForValidation();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $commonRules = parent::rules();

        return array_merge($commonRules, [
            'document_name' => [
                'required',
                'string',
                'max:255',
            ],
        ]);
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        $commonAttributes = parent::attributes();

        return array_merge($commonAttributes, [
            'document_name' => __('Document name'),
        ]);
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
        // Format error messages and response
        $transformService = new TransformValidationErrorService($validator->errors());
        $errors = $transformService->formatErrors();
        $response = response()->respondInvalidParameters($errors);

        throw new HttpResponseException($response);
    }
}
