<?php

namespace App\Http\Requests\Schedule;

use App\Rules\ValidTimeRangeRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpsertScheduleRequest extends FormRequest
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
            'owner_rio_id' => [
                'nullable',
                'integer',
                'exists:rios,id',
            ],
            'owner_neo_id' => [
                'nullable',
                'integer',
                'exists:neos,id',
            ],
            'schedule_title' => [
                'required',
                'string',
                'max:100',
            ],
            'start_date' => [
                'required',
                'date_format:Y-m-d',
            ],
            'end_date' => [
                'required',
                'date_format:Y-m-d',
                'after_or_equal:start_date',
            ],
            'start_time' => [
                'nullable',
                'required_with:end_time',
                'required_if:all_day,0,false',
                'date_format:H:i',
            ],
            'end_time' => [
                'nullable',
                'required_with:start_time',
                'required_if:all_day,0,false',
                'date_format:H:i',
                new ValidTimeRangeRule($this),
            ],
            'caption' => [
                'nullable',
            ],
            'meeting_url' => [
               'nullable',
               'url',
            ],
            'all_day' => [
                'nullable',
                'boolean',
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
            'start_date.date_format' => __('validation.date'),
            'end_date.date_format' => __('validation.date'),
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
