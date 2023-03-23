<?php

namespace App\Http\Requests\Classified;

use App\Objects\ProductEditRegistration;

class ProductEditCompleteRequest extends ProductEditRequest
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
        $session = ProductEditRegistration::getSession();
        $registrationInput = $session->data;

        $this->merge([
            'sale_category' => $registrationInput->sale_category ?? null,
            'title' => $registrationInput->title ?? null,
            'detail' => $registrationInput->detail ?? null,
            'price' => $registrationInput->price ?? null,
            'is_public' => $registrationInput->is_public ?? null,
            'set_quote' => $registrationInput->set_quote ?? null,
            'upload_file' => (array) ($registrationInput->upload_file ?? []),
            'local_file' => (array) ($registrationInput->local_file ?? []),
        ]);
    }
}
