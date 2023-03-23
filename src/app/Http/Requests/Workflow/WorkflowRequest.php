<?php

namespace App\Http\Requests\Workflow;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use App\Enums\Workflow\PriorityTypes;
use App\Objects\ServiceSelected;
use App\Models\NeoBelong;
use App\Rules\ValidFilepondCode;
use App\Rules\ValidUploadSize;

class WorkflowRequest extends FormRequest
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
        $attaches = $this->input('attaches', []);
        $uploadFiles = $this->input('upload_file', []);
        $attachmentCount = 0;

        if (is_array($attaches)) {
            $attachmentCount += count($attaches);
        }

        if (is_array($uploadFiles)) {
            $attachmentCount += count($uploadFiles);
        }

        $this->merge([
            'attachment_count' => $attachmentCount,
        ]);
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
            'workflow_title' => 'required|string|max:100',
            'caption' => 'required|string',
            'priority' => [
                'required',
                'string',
                Rule::in(PriorityTypes::getValues())
            ],
            'approvers' => [
                'required',
                'array'
            ],
            'approvers.*.id' => [
                'required',
                'distinct',
                Rule::in($neos)
            ],
            'attaches' => 'required_without:upload_file|nullable|array',
            'attaches.*' => 'array',
            'attaches.*.document_id' => 'integer',
            'upload_file' => [
                'required_without:attaches',
                'nullable',
                'array',
                new ValidUploadSize(),
            ],
            'upload_file.*' => [
                'string',
                new ValidFilepondCode(),
            ],
            'attachment_count' => 'required|numeric|max:5'
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
            'attaches',
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
            'workflow_title' => __('Title'),
            'caption' => __('Explanation'),
            'priority' => __('Priority'),
            'approvers' => __('Approver'),
            'attaches' => __('Attaches'),
            'upload_file' => __('Attaches'),
            'attachment_count' => __('Attaches'),
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'attaches.required_without' => __('validation.required'),
            'upload_file.required_without' => __('validation.required'),
            'attachment_count.max' => __('validation.max.array'),
        ];
    }

    /**
     * Get the validated data from the request.
     *
     * @return array
     */
    public function validated()
    {
        $requestData = $this->validator->validated();

        // Parse document object to ids
        if (!empty($requestData['attaches'])) {
            $documents = collect($requestData['attaches']);
            $requestData['attaches'] = $documents
                ->pluck('document_id')
                ->toArray();
        }

        return $requestData;
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
