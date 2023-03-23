<?php

namespace App\Http\Requests\Neo;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\Neo\RoleType;

class UpdateMemberRolesRequest extends FormRequest
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
            'rio_id' => 'required|exists:rios,id',
            'type' => [
                'filled',
                Rule::in(RoleType::getValues())
            ]
        ];
    }
}
