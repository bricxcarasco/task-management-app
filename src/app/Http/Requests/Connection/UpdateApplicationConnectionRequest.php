<?php

namespace App\Http\Requests\Connection;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\ServiceSelectionTypes;

class UpdateApplicationConnectionRequest extends FormRequest
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
            'id' => 'required',
            'connection_id' => 'required',
            'service' => [
                'required',
                Rule::in(ServiceSelectionTypes::getValues())
            ]
        ];
    }
}
