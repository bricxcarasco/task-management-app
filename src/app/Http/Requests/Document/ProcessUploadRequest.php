<?php

namespace App\Http\Requests\Document;

use App\Helpers\CommonHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\UploadedFile;

class ProcessUploadRequest extends FormRequest
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
            'upload_file' => [
                'required',
                'array',
            ],
            'upload_file.*' => [
                function ($attribute, $value, $fail) {
                    $message = __('validation.general', [
                        'attribute' => __('File')
                    ]);

                    // Check if uploaded image file
                    if ($value instanceof UploadedFile) {
                        return;
                    }

                    // Check if JSON string usually due to chunk upload
                    if (CommonHelper::isJson($value)) {
                        return;
                    }

                    $fail($message);
                },
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
            'upload_file' => __('File'),
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
