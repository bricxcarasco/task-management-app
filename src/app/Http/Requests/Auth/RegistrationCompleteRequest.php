<?php

namespace App\Http\Requests\Auth;

use App\Enums\OTPTypes;
use App\Http\Requests\RegistrationInputRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class RegistrationCompleteRequest extends RegistrationInputRequest
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
        // Inject registration input to request data for revalidation
        $registrationInput = $this->session()->get('registration.users.input');

        $this->merge(array_merge(
            $registrationInput['rio'] ?? [],
            $registrationInput['user'] ?? [],
            $registrationInput['rio_profile'] ?? [],
            $registrationInput['other'] ?? []
        ));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // Get registration input rules to inject to registration completion validation
        $registrationInputRules = parent::rules();

        return array_merge($registrationInputRules, [
            'identifier' => 'required|string',
            'code' => 'required|array',
            'code.*' => 'required|integer',
            'otp_type' => [
                'required',
                'integer',
                Rule::in(OTPTypes::getValues()),
            ],
        ]);
    }

    /**
     * Get the validated data from the request.
     *
     * @return array
     */
    public function validated()
    {
        $validated = parent::validated();

        // Combine digits to a single value
        $validated['code'] = implode('', $validated['code']);

        return $validated;
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function failedValidation(Validator $validator)
    {
        // Get field keys of registration
        $registrationInputKeys = array_keys(parent::rules());

        // Get all failed fields as keys
        $validatorKeys = $validator->getMessageBag()->keys();

        // Get failed registration input fields
        $failedRegistrationInputs = array_intersect($validatorKeys, $registrationInputKeys);

        // Redirect to registration form when registration input are invalid
        if (!empty($failedRegistrationInputs)) {
            $registrationFormLink = $this->session()->get('registration.users.input.registration_url');

            $response = redirect($registrationFormLink)
                ->withErrors($validator);
        } else {
            // Setup validation error response for SMS auth code failure
            $response = redirect()
                ->route('registration.sms.index')
                ->withAlertBox('danger', __('messages.sms_auth_code_verify_failed'));
        }

        // Throw validation error
        throw (new ValidationException($validator, $response))
            ->errorBag($this->errorBag)
            ->redirectTo($this->getRedirectUrl());
    }
}
