<?php

namespace App\Http\Requests\Classified;

use App\Enums\Classified\MessageSender;
use App\Rules\ValidFilepondCode;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

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
            'classified_contact_id' => 'required|integer',
            'sender' => [
                'required',
                'integer',
                Rule::in(MessageSender::getValues())
            ],
            'message' => 'required_without_all:attaches,upload_file|max:2500',
            'attaches' => 'nullable|array',
            'attaches.*' => 'string',
            'upload_file' => 'nullable|array',
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
            'classified_contact_id' => __('Contact ID'),
            'sender' => __('Sender'),
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
