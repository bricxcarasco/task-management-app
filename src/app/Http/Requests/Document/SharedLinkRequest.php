<?php

namespace App\Http\Requests\Document;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use App\Services\TransformValidationErrorService;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use App\Enums\ServiceSelectionTypes;
use App\Models\Document;
use App\Models\Neo;
use App\Models\Rio;

class SharedLinkRequest extends FormRequest
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
            'document_id' => [
                'required',
                Rule::exists(Document::class, 'id')
                    ->whereNull('deleted_at'),
            ],
            'service' => [
                'required',
                Rule::in(ServiceSelectionTypes::getValues()),
            ],
            'rio_id' => [
                'required_if:service,' . ServiceSelectionTypes::RIO,
                Rule::exists(Rio::class, 'id')
                    ->whereNull('deleted_at'),
            ],
            'neo_id' => [
                'required_if:service,' . ServiceSelectionTypes::NEO,
                Rule::exists(Neo::class, 'id')
                    ->whereNull('deleted_at'),
            ],
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
