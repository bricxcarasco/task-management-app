<?php

namespace App\Http\Requests\Document;

use App\Models\DocumentAccess;
use App\Services\TransformValidationErrorService;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DocumentShareSettingRequest extends FormRequest
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
        $requestData = $this->all();

        return [
            'id' => 'required|integer|exists:documents,id',
            'rio_id.*' => [
                'nullable',
                'integer',
                'exists:rios,id',
                Rule::unique(DocumentAccess::class, 'rio_id')
                    ->where('document_id', $requestData['id'])
                    ->where('deleted_at', null),
            ],
            'neo_id.*' => [
                'nullable',
                'integer',
                'exists:neos,id',
                Rule::unique(DocumentAccess::class, 'neo_id')
                    ->where('document_id', $requestData['id'])
                    ->where('deleted_at', null),
            ],
            'neo_group_id.*' => [
                'nullable',
                'integer',
                'exists:neo_groups,id',
                Rule::unique(DocumentAccess::class, 'neo_group_id')
                    ->where('document_id', $requestData['id'])
                    ->where('deleted_at', null),
            ],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'rio_id.*.unique' => __('Share access is already given', ['service' => __('RIO ID')]),
            'neo_id.*.unique' => __('Share access is already given', ['service' => __('NEO ID')]),
            'neo_group_id.*.unique' => __('Share access is already given', ['service' => __('NEO Group  ID')]),
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
            'id' => __('Directory ID'),
            'rio_id.*' => __('RIO ID'),
            'neo_id.*' => __('NEO ID'),
            'neo_group_id.*' => __('NEO Group ID'),
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
