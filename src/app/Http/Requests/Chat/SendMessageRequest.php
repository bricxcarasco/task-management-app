<?php

namespace App\Http\Requests\Chat;

use App\Rules\ValidFilepondCode;
use App\Rules\ValidUploadSize;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class SendMessageRequest extends FormRequest
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
            'chat_id' => 'required|integer',
            'chat_participant_id' => 'required|integer',
            'message' => 'required_without_all:attaches,upload_file|max:2500',
            'attaches' => 'nullable|array',
            'attaches.*' => 'string',
            'upload_file' =>  [
                'nullable',
                'array',
                new ValidUploadSize(),
            ],
            'upload_file.*' => [
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
            'chat_id' => __('Chat ID'),
            'chat_participant_id' => __('Chat Participant ID'),
            'message' => __('Message'),
            'attaches' => __('Attaches'),
            'upload_file' => __('Attaches'),
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
