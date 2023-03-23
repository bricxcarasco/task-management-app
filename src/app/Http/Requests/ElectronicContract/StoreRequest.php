<?php

namespace App\Http\Requests\ElectronicContract;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\ServiceSelectionTypes;
use App\Rules\ValidFilepondCode;

class StoreRequest extends FormRequest
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
            'sender_email' => 'required|email:filter',
            'selected_document_id' => 'required_without:local_file|nullable|integer|exists:documents,id',
            'local_file' => [
                'required_without:selected_document_id',
                'string',
                new ValidFilepondCode(),
            ],
            'selected_connection_id' => 'filled|integer',
            'selected_connection_type' => [
                'required_with:selected_connection_id',
                Rule::in(ServiceSelectionTypes::getValues()),
            ],
            'recipient_name' => 'filled|string',
            'recipient_email' => 'required|email:filter',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'selected_document_id.required_without' => __('validation.required'),
            'local_file.required_without' => __('validation.required'),
            'selected_connection_id.filled' => __('validation.required'),
            'selected_connection_type.required_with' => __('validation.required'),
            'recipient_name.filled' => __('validation.required'),
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
            'sender_email' => __('Email Address'),
            'selected_document_id' => __('File'),
            'local_file' => __('File'),
            'selected_connection_id' => __('Electronic Contract Destination'),
            'selected_connection_type' => __('Electronic Contract Destination'),
            'recipient_name' => __('Electronic Contract Destination'),
            'recipient_email' => __('Electronic Contract Destination'),
        ];
    }
}
