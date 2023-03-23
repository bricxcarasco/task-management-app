<?php

namespace App\Http\Requests\Neo;

use App\Enums\Neo\AcceptParticipationType;
use App\Enums\Neo\RestrictConnectionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePrivacyRequest extends FormRequest
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
            'accept_connections' => [
                'required',
                'integer',
                Rule::in(RestrictConnectionType::getValues())
            ],
            'accept_belongs' => [
                'required',
                'integer',
                Rule::in(AcceptParticipationType::getValues())
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
            'accept_connections' => __('Restrictions on accepting connection applications'),
            'accept_belongs' => __('Participation application acceptance restrictions'),
        ];
    }
}
