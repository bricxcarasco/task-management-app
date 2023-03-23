<?php

namespace App\Http\Requests\Auth;

use Crypt;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ResetEmailVerifyRequest extends FormRequest
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
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        // Get encrypted user
        $encryptedUser = $this->route('user');

        // Initialize request data based on route params
        $requestData = [
            'verification' => $this->route('verification'),
            'token' => $this->route('token'),
        ];

        // Decrypt user id
        if (!empty($encryptedUser)) {
            try {
                $requestData['user'] = Crypt::decrypt($encryptedUser);
            } catch (\Throwable $th) {
            }
        }

        // Merge route params as request data
        $this->merge($requestData);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user' => 'required',
            'verification' => 'required',
            'token' => 'required',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     */
    protected function failedValidation(Validator $validator)
    {
        $response = response()->view('auth.reset_email.invalid');
        throw new HttpResponseException($response);
    }
}
