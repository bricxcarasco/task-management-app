<?php

namespace App\Http\Requests\Connection;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\Connection\ListFilters;
use Illuminate\Validation\Rule;

class FetchConnectionListRequest extends FormRequest
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
            'mode' => ['nullable', Rule::in(ListFilters::getValues())],
        ];
    }
}
