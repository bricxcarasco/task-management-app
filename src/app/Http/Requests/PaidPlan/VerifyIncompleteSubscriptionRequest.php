<?php

namespace App\Http\Requests\PaidPlan;

use Illuminate\Foundation\Http\FormRequest;

class VerifyIncompleteSubscriptionRequest extends FormRequest
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
            'stripe_customer_id' => [
                'required',
                'string',
                'exists:subscribers,stripe_customer_id'
            ],
            'stripe_subscription_id' => [
                'required',
                'string',
                'exists:subscribers,stripe_subscription_id'
            ],
            'stripe_client_secret' => [
                'required',
                'string',
                'exists:subscribers,stripe_client_secret'
            ]
        ];
    }
}
