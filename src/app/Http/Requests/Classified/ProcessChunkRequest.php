<?php

namespace App\Http\Requests\Classified;

use App\Rules\ValidFilepondCode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProcessChunkRequest extends FormRequest
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
        // Set headers and content for validation
        $this->merge([
            'offset' => $this->header('upload-offset'),
            'length' => $this->header('upload-length'),
            'filename' => $this->header('upload-name'),
            'content' => $this->content,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'patch' => [
                'required',
                'string',
                new ValidFilepondCode(),
            ],
            'offset' => 'required|integer',
            'length' => 'required|integer',
            'filename' => 'required|string',
            'content' => 'required',
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
            'patch' => __('Server generated code'),
            'offset' => 'Upload-Offset',
            'length' => 'Upload-Length',
            'filename' => 'Upload-Name',
            'content' => __('Request Body'),
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
