<?php

namespace App\Http\Requests\ElectronicContract;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CmSignCallbackRequest extends FormRequest
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
        // Merge route parameters before validation
        $this->merge([
            'id' => $this->json('id'),
            'type' => $this->json('type'),
            'dossier_id' => $this->json('dossier.id'),
            'dossier_state' => $this->json('dossier.state'),
            'created' => $this->json('created'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'string|max:255',
            'type' => 'string|max:255',
            'dossier_id' => 'string|max:255',
            'dossier_state' => 'string|max:255',
            'created' => 'nullable',
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
