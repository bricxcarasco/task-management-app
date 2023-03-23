<?php

namespace App\Http\Requests\WorkflowAction;

use Illuminate\Validation\Rule;
use App\Enums\Workflow\ReactionTypes;
use Illuminate\Foundation\Http\FormRequest;

class UpdateReactionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'reaction' => [
                'required',
                Rule::in(ReactionTypes::getReactionWithoutPendingForRule())
            ],
            'comment' => [
                'string',
                'max:1000',
                'required_if:reaction,' . ReactionTypes::RETURNED . ',' . ReactionTypes::REJECTED,
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
        $reaction = $this->reaction ?? null;
        return [
            'comment.required_if' => __('validation.required_if', [
                'value' => ReactionTypes::getDescription($reaction),
            ]),
        ];
    }
}
