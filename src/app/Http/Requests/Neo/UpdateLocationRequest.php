<?php

namespace App\Http\Requests\Neo;

use App\Enums\PrefectureTypes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class UpdateLocationRequest extends FormRequest
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
            'city' => 'nullable|max:255',
            'address' => 'nullable|max:255',
            'building' => 'nullable|max:255',
            'prefecture' => [
                'required',
                'integer',
                Rule::in(PrefectureTypes::getValues())
            ],
            'nationality' => [
                'required_if:prefecture,0',
                'max:255',
            ],
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
            'prefecture' => __('Prefecture of Residences'),
            'city' => __('City'),
            'address' => __('Address'),
            'building' => __('Building'),
            'nationality' => __('Country'),
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
        $response = response()->json($validator->getMessageBag(), Response::HTTP_BAD_REQUEST);

        throw new HttpResponseException($response);
    }
}
