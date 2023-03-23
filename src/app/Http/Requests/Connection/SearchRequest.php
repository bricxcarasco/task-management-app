<?php

namespace App\Http\Requests\Connection;

use App\Enums\YearsOfExperiences;
use App\Enums\EntityType;
use App\Enums\ConnectionInclusion;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class SearchRequest extends FormRequest
{
    /**
     * The route to redirect to if validation fails.
     *
     * @var string
     */
    protected $redirectRoute = 'connection.search.search';

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
            'search_target' => ['required', Rule::in(EntityType::getValues())],
            'years_of_experience' => ['nullable', Rule::in(YearsOfExperiences::getValues())],
            'business_category' => 'nullable|exists:business_categories,id',
            'free_word' => 'nullable|string|max:50',
            'exclude_connected' => ['nullable', Rule::in(ConnectionInclusion::getValues())],
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
            'search_target' => __('Search Target'),
            'years_of_experience' => __('Years of Experience'),
            'business_category' => __('Industry'),
            'free_word' => __('Free word'),
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
        if ($this->isJson()) {
            $response = response()->respondInvalidParameters($validator->errors());

            throw new HttpResponseException($response);
        }

        parent::failedValidation($validator);
    }
}
