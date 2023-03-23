<?php

namespace App\Http\Requests\Document;

use App\Enums\Document\StorageTypes;
use App\Rules\ValidFilepondCode;
use App\Rules\ValidUploadSize;
use App\Services\TransformValidationErrorService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UploadFileRequest extends FormRequest
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
            'directory_id' => 'nullable|int',
            'storage_type_id' => [
                'required',
                'integer',
                Rule::in(StorageTypes::getValues())
            ],
            'code' => [
                'required',
                'array',
                new ValidUploadSize(),
            ],
            'code.*' => [
                'required',
                'string',
                new ValidFilepondCode(),
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
            'code' => __('Server generated code'),
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
        $transformService = new TransformValidationErrorService($validator->errors());
        $errors = $transformService->formatErrors();
        $response = response()->respondInvalidParameters($errors);

        throw new HttpResponseException($response);
    }
}
