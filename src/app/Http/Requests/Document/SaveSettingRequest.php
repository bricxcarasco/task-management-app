<?php

namespace App\Http\Requests\Document;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use App\Services\TransformValidationErrorService;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use App\Rules\Document\CheckShareTypeCondition;
use App\Enums\Document\DocumentShareType;

class SaveSettingRequest extends FormRequest
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
            'id' => 'required|integer',
            'document_id' => 'required|integer|exists:documents,id',
            'share_type' => [
                'required',
                'integer',
                Rule::in(DocumentShareType::getValues()),
                new CheckShareTypeCondition($this)
            ]
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
        // Format error messages and response
        $transformService = new TransformValidationErrorService($validator->errors());
        $errors = $transformService->formatErrors();
        $response = response()->respondInvalidParameters($errors);

        throw new HttpResponseException($response);
    }
}
