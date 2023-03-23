<?php

namespace App\Http\Requests\Task;

use App\Enums\Task\Priorities;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class UpsertTaskRequest extends FormRequest
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
        $now = Carbon::now()->format('Y-m-d');
        $currentTime = Carbon::now()->format('H:i');

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
            'task_title' => [
                'required',
                'string',
                'max:100',
            ],
            'limit_date' => [
                'nullable',
                'required_with:limit_time',
                'date_format:Y-m-d',
                'after_or_equal:' . $now
            ],
            'limit_time' => [
                'nullable',
                'required_with:limit_date',
                'date_format:H:i',
                'after:' . $currentTime
            ],
            'priority' => [
                'nullable',
                Rule::in(Priorities::getValues()),
            ],
            'remarks' => [
                'nullable',
                'string'
            ],
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
