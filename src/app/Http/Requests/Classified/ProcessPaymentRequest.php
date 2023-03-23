<?php

namespace App\Http\Requests\Classified;

use Illuminate\Foundation\Http\FormRequest;

class ProcessPaymentRequest extends FormRequest
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
            'payment_id' => [
                'required',
                'integer',
                'exists:classified_payments,id',
            ],
            'intent' => [
                'required',
                'string',
            ],
        ];
    }
}
