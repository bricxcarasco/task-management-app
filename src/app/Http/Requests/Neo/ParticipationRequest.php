<?php

namespace App\Http\Requests\Neo;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use App\Services\TransformValidationErrorService;
use Illuminate\Http\Exceptions\HttpResponseException;

class ParticipationRequest extends FormRequest
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
            'id' => 'filled|integer|exists:neo_belongs,id',
            'rio_id' => 'filled|integer|exists:neo_belongs,rio_id',
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
        // Format error messages and response
        $transformService = new TransformValidationErrorService($validator->errors());
        $errors = $transformService->formatErrors();
        $response = response()->respondInvalidParameters($errors);

        throw new HttpResponseException($response);
    }
}
