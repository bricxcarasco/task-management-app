<?php

namespace App\Http\Requests\Rio;

use App\Enums\Neo\AcceptBelongType;
use App\Enums\Neo\AcceptConnectionType;
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
                Rule::in(AcceptConnectionType::getValues())
            ],
            'accept_connections_by_neo' => [
                'required',
                'integer',
                Rule::in(AcceptBelongType::getValues())
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
            'accept_connections' => __('Personal restrictions'),
            'accept_connections_by_neo' => __('Restrictions by participating NEOs'),
        ];
    }
}
