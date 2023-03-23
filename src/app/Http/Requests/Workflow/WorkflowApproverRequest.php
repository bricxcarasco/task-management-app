<?php

namespace App\Http\Requests\Workflow;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use App\Objects\ServiceSelected;
use App\Models\NeoBelong;

class WorkflowApproverRequest extends FormRequest
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
        /** @var \App\Models\User */
        $user = auth()->user();

        // Get subject selected session
        $service = ServiceSelected::getSelected();

        $neos = NeoBelong::where('neo_id', $service->data->id)
            ->where('rio_id', '<>', $user->rio_id)
            ->pluck('rio_id')
            ->toArray();

        return [
            'approvers' => 'array',
            'approvers.*.id' => [
                'required',
                'distinct',
                Rule::in($neos)
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
            'approvers' => __('Approver'),
            'approvers.*.id' => __('Approver')
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
