<?php

namespace App\Http\Requests\Document;

use App\Enums\Document\DocumentTypes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DocumentListRequest extends FormRequest
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
            'search' => 'nullable|max:255|string',
            'document_type' => [
                'nullable',
                Rule::in(DocumentTypes::getValues()),
            ]
        ];
    }
}
