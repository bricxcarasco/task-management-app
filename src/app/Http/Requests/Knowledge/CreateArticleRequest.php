<?php

namespace App\Http\Requests\Knowledge;

use Illuminate\Validation\Rule;
use App\Enums\Knowledge\ArticleTypes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateArticleRequest extends FormRequest
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
            'directory_id' => 'nullable|integer|exists:knowledges,id',
            'task_title' => 'required|string|max:255',
            'contents' => 'required|string',
            'reference_url_0' => 'nullable|url',
            'reference_url_1' => 'nullable|url',
            'reference_url_2' => 'nullable|url',
            'reference_url_3' => 'nullable|url',
            'reference_url_4' => 'nullable|url',
            'is_draft' => ['required', Rule::in(ArticleTypes::getValues())]
        ];
    }

    /**
     * forms register attributes
     *
     * @return array request
     */
    public function formAttributes()
    {
        return $this->except([
            'reference_url_0',
            'reference_url_1',
            'reference_url_2',
            'reference_url_3',
            'reference_url_4',
        ]);
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'contents' =>  __('Content'),
            'reference_url_0' => __('Reference URL'),
            'reference_url_1' => __('Reference URL'),
            'reference_url_2' => __('Reference URL'),
            'reference_url_3' => __('Reference URL'),
            'reference_url_4' => __('Reference URL'),
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
