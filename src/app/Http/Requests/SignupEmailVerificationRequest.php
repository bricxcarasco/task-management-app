<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ValidEmailFormat;

class SignupEmailVerificationRequest extends FormRequest
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
            'email' => [
                'required',
                'string',
                'email:filter',
                'max:255',
                'unique:users,email',
                new ValidEmailFormat()
            ],
            'rd_code' => [
                'nullable',
                'string',
                'max:255'
            ],
            'a8' => [
                'nullable',
                'string',
                'max:255'
            ]
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        // Merge route parameters before validation
        $this->merge([
            'email' => $this->email,
        ]);
    }
}
