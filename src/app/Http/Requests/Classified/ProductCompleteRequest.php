<?php

namespace App\Http\Requests\Classified;

use App\Objects\ProductRegistration;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class ProductCompleteRequest extends ProductRegistrationRequest
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
        $registrationInput = ProductRegistration::getSession();

        $this->merge([
            'sale_category' => $registrationInput->sale_category ?? null,
            'title' => $registrationInput->title ?? null,
            'detail' => $registrationInput->detail ?? null,
            'price' => $registrationInput->price ?? null,
            'is_public' => $registrationInput->is_public ?? null,
            'set_quote' => $registrationInput->set_quote ?? null,
            'upload_file' => $registrationInput->upload_file ?? [],
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
        // Get inputs
        $inputs = $this->all();

        // Prepare response
        $response = redirect()
            ->route($this->redirectRoute)
            ->withErrors($validator)
            ->withInput($inputs);

        // Throw validation error
        throw (new ValidationException($validator, $response))
            ->errorBag($this->errorBag);
    }
}
