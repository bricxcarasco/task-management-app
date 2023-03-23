<?php

namespace App\Http\Requests\Rio;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateTelephoneRequest extends FormRequest
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
        /** @var User */
        $user = auth()->user();
        $rio = $user->rio;

        // Guard clause for non-existing rio
        if (empty($rio)) {
            abort(404);
        }

        return [
            'tel' => [
                'required',
                'phone:AUTO,JP',
                'exclude_if:tel,' . $rio->tel,
                Rule::unique('rios', 'tel')->ignore($user->rio_id)
            ],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'tel.unique' => __('messages.validation_error_existing_phone_number')
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
