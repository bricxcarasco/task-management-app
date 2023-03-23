<?php

namespace App\Http\Requests\Form;

use App\Helpers\CommonHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\UploadedFile;

class BasicSettingProcessUploadRequest extends FormRequest
{
    /**
     * Accepted File Types
     *
     * @var array
     */
    private $acceptedFileTypes = [
        'image/jpeg',
        'image/jpg',
        'image/pjpeg',
        'image/png',
    ];

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
            'image' => [
                'required',
                'array',
            ],
            'image.*' => [
                function ($attribute, $value, $fail) {
                    $message = __('validation.general', [
                        'attribute' => __('Issuer Logo')
                    ]);

                    // Check if uploaded image file
                    if ($value instanceof UploadedFile) {
                        $mimeType = $value->getClientMimeType();

                        if (in_array($mimeType, $this->acceptedFileTypes)) {
                            return;
                        }
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
            'image' => __('Issuer Logo'),
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
